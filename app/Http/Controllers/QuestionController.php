<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\question;
class QuestionController extends Controller
public function __construct()
    {
       $this->middleware('guest');
    }

{
    public function home(){
		$question = question::all();
		return view('question', ['question' => $question]);
	}
	public function add(Request $request){
		$this->validate($request, [
		'q1' => 'required',
		'q2' => 'required',
		'q3' => 'required',
		'q4' => 'required',
		'q5' => 'required',
		'q6' => 'required',
		'q7' => 'required',
		'q8' => 'required',
		'q9' => 'required',
		'q10' => 'required'
		
	      ]);
		$question = new question;
		$question->q1 = $request->input('q1');
		$question->q2 = $request->input('q2');
		$question->q3 = $request->input('q3');
		$question->q4 = $request->input('q4');
		$question->q5 = $request->input('q5');
		$question->q6 = $request->input('q6');
		$question->q7 = $request->input('q7');
		$question->q8 = $request->input('q8');
		$question->q9 = $request->input('q9');
		$question->q10 = $request->input('q10');
		
	
		$question->save();
		return redirect('/')->with('info', 'YOur score is......');
	}
	
	public function update($id){
     $question = question::all($id);
	
	 
	return view('update', ['question' => $question]);
}
}
