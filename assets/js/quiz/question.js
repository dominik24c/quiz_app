import Answer from "./answer";
import Validator from "../utils/validator";
import axios from "axios";

export default class Question{
    constructor() {
        $('#add-question-btn').click(this.addQuestion.bind(this,null));
    }

    addQuestion(questionObj=null){
        // create question div
        const question=`
            <div class="question">
               <button type="button" class="delete-question-btn delete-btn">x</button>
               <div class="form-control">
                    <label for="question">Question:</label>
                    <input type="text" name="question" value="${questionObj ? questionObj.question : ""}">
                </div>
                <div class="form-control">
                    <label for="points">Points:</label>
                    <input type="text" name="points" placeholder="1 - 9999" value="${questionObj ? questionObj.points : ""}">
                </div>
                <div class="answers">
                </div>
                <div class="answers-error"></div>
                <button type="button" class="add-btn add-answer-btn">Add Answer</button>
           </div>
        `;

        $('#questions').append(question);

        //add delete answer button handler
        $('.delete-question-btn').on('click',function (){
            $(this).parent().remove();
            $('#question-error').html('');
        });

        const lastQuestion = $('.question').last();
        // reset error and on focus handler for input error
        Validator.addOnFocusRemoveFormError(lastQuestion.find('input[name="question"]'),'form-error');
        Validator.addOnFocusRemoveFormError(lastQuestion.find('input[name="points"]'),'form-error');
        $('#question-error').html('');

        if(questionObj){
            console.log(questionObj)
            lastQuestion.attr('data-index',questionObj.id);
            const answer = new Answer(lastQuestion);
            questionObj.answers.forEach(answerObj=>{
                // console.log(answerObj)
                answer.addAnswer(lastQuestion,answerObj)
            });
        }
        else{
            new Answer(lastQuestion);
        }
    }

    getQuestions()
    {
       axios.get(`${window.location.pathname}/quiz-data`)
           .then(response=> {
               console.log(response.data);
               $('#quiz').attr('data-index',response.data.id)
               const questions = response.data.questions;
               questions.forEach(question=>{
                    this.addQuestion(question);
               });
               $('#loading-spinner').hide();
           }).catch(error=>{
               console.log(error);
       })
    }
}