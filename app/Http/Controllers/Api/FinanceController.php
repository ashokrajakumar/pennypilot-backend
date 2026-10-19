<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Emi;
use App\Models\EmiPayment;
use App\Models\Expense;
use App\Models\Salary;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class FinanceController extends Controller
{
    private function period(Request $request): array
    {
        $v=$request->validate(['month'=>['required','integer','between:1,12'],'year'=>['required','integer','between:2000,2100']]);
        return [(int)$v['month'],(int)$v['year']];
    }
    private function monthData(int $month,int $year): array
    {
        $salary=(float) Salary::where('month',$month)->where('year',$year)->value('amount');
        $expenses=(float) Expense::whereYear('expense_date',$year)->whereMonth('expense_date',$month)->sum('amount');
        $paid=(float) EmiPayment::whereYear('due_date',$year)->whereMonth('due_date',$month)->where('status','paid')->sum('amount');
        return ['salary'=>$salary,'expenses'=>$expenses,'emi_paid'=>$paid,'balance'=>round($salary-$expenses-$paid,2)];
    }
    public function test(){return response()->json(['success'=>true,'message'=>'PennyPilot API is working']);}
    public function dashboard(Request $request)
    {
        [$month,$year]=$this->period($request);
        $summary=$this->monthData($month,$year);
        $emis=$this->listEmisForPeriod($month,$year);
        $due=$emis->filter(fn($e)=>$e['is_due'])->sum(fn($e)=>(float)$e['monthly_amount']);
        $pending=$emis->filter(fn($e)=>$e['is_due']&&!$e['is_paid'])->sum(fn($e)=>(float)$e['monthly_amount']);
        $recent=Expense::whereYear('expense_date',$year)->whereMonth('expense_date',$month)->orderByDesc('expense_date')->orderByDesc('id')->limit(5)->get();
        return response()->json(['data'=>array_merge($summary,['emi_due'=>$due,'emi_pending'=>$pending,'recent_expenses'=>$recent])]);
    }
    public function reports(Request $request)
    {
        [$month,$year]=$this->period($request);
        $categories=Expense::select('category')->selectRaw('SUM(amount) as total')->whereYear('expense_date',$year)->whereMonth('expense_date',$month)->groupBy('category')->orderByDesc('total')->get();
        return response()->json(['data'=>array_merge($this->monthData($month,$year),['categories'=>$categories])]);
    }
    public function salaries(){return response()->json(['data'=>Salary::orderByDesc('year')->orderByDesc('month')->get()]);}
    public function saveSalary(Request $request)
    {
        $v=$request->validate(['amount'=>['required','numeric','min:0','max:9999999999.99'],'month'=>['required','integer','between:1,12'],'year'=>['required','integer','between:2000,2100']]);
        $row=Salary::updateOrCreate(['month'=>$v['month'],'year'=>$v['year']],['amount'=>$v['amount']]);
        return response()->json(['message'=>'Salary saved','data'=>$row]);
    }
    public function deleteSalary(Salary $salary){$salary->delete();return response()->json(['message'=>'Salary deleted']);}
    public function expenses(Request $request)
    {
        [$month,$year]=$this->period($request);
        return response()->json(['data'=>Expense::whereYear('expense_date',$year)->whereMonth('expense_date',$month)->orderByDesc('expense_date')->orderByDesc('id')->get()]);
    }
    private function expenseRules($partial=false):array
    {
        $req=$partial?'sometimes|required':'required';
        return ['title'=>[$req,'string','max:150'],'category'=>[$req,'string','max:100'],'amount'=>[$req,'numeric','gt:0','max:9999999999.99'],'expense_date'=>[$req,'date_format:Y-m-d'],'description'=>['nullable','string','max:1000']];
    }
    public function addExpense(Request $request){$row=Expense::create($request->validate($this->expenseRules()));return response()->json(['message'=>'Expense added','data'=>$row],201);}
    public function updateExpense(Request $request,Expense $expense){$expense->update($request->validate($this->expenseRules(true)));return response()->json(['message'=>'Expense updated','data'=>$expense]);}
    public function deleteExpense(Expense $expense){$expense->delete();return response()->json(['message'=>'Expense deleted']);}
    private function emiRules():array
    {
        return ['name'=>['required','string','max:150'],'monthly_amount'=>['required','numeric','gt:0','max:9999999999.99'],'total_amount'=>['required','numeric','gt:0','max:9999999999.99'],'total_months'=>['required','integer','between:1,600'],'start_date'=>['required','date_format:Y-m-d'],'due_day'=>['required','integer','between:1,28'],'status'=>['required',Rule::in(['active','completed'])]];
    }
    private function dueDate(Emi $emi,int $month,int $year):?Carbon
    {
        $start=Carbon::parse($emi->start_date)->startOfMonth();
        $target=Carbon::create($year,$month,1)->startOfMonth();
        $difference=$start->diffInMonths($target,false);
        if($difference<0 || $difference>= $emi->total_months || $emi->status==='completed')return null;
        return Carbon::create($year,$month,(int)$emi->due_day);
    }
    private function listEmisForPeriod(int $month,int $year)
    {
        return Emi::with('payments')->orderByDesc('id')->get()->map(function($emi)use($month,$year){
            $due=$this->dueDate($emi,$month,$year);
            $payment=$emi->payments->first(fn($p)=>Carbon::parse($p->due_date)->format('Y-m')===sprintf('%04d-%02d',$year,$month));
            return array_merge($emi->toArray(),['paid_count'=>$emi->payments->where('status','paid')->count(),'is_due'=>$due!==null,'is_paid'=>$payment?->status==='paid']);
        });
    }
    public function emis(Request $request){[$month,$year]=$this->period($request);return response()->json(['data'=>$this->listEmisForPeriod($month,$year)]);}
    public function addEmi(Request $request){$row=Emi::create($request->validate($this->emiRules()));return response()->json(['message'=>'EMI added','data'=>$row],201);}
    public function updateEmi(Request $request,Emi $emi)
    {
        $v=$request->validate($this->emiRules());
        if($emi->payments()->where('status','paid')->exists() && ($v['start_date'] !== $emi->start_date->format('Y-m-d') || (int)$v['due_day']!==$emi->due_day || (int)$v['total_months']!==$emi->total_months || (float)$v['monthly_amount']!==(float)$emi->monthly_amount)){
            return response()->json(['message'=>'Cannot change installment schedule after payments are recorded. Undo payments first.'],422);
        }
        $emi->update($v);return response()->json(['message'=>'EMI updated','data'=>$emi]);
    }
    public function deleteEmi(Emi $emi){$emi->delete();return response()->json(['message'=>'EMI deleted']);}
    public function payEmi(Request $request,Emi $emi)
    {
        $v=$request->validate(['month'=>['required','integer','between:1,12'],'year'=>['required','integer','between:2000,2100'],'paid'=>['required','boolean']]);
        $due=$this->dueDate($emi,(int)$v['month'],(int)$v['year']);
        if(!$due)return response()->json(['message'=>'This EMI has no installment due for that month.'],422);
        $row=DB::transaction(function()use($emi,$due,$v){
            return EmiPayment::updateOrCreate(['emi_id'=>$emi->id,'due_date'=>$due->format('Y-m-d')],['amount'=>$emi->monthly_amount,'status'=>$v['paid']?'paid':'pending','paid_date'=>$v['paid']?now()->toDateString():null]);
        });
        return response()->json(['message'=>'Payment status updated','data'=>$row]);
    }
}
