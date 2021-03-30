import Answer from "./answer";
import Validator from "./validator";

export default class Question{
    constructor() {
        $('#add-question-btn').click(this.addQuestion);
    }

    addQuestion(){
        // create question div
        const question=`
            <div class="question">
               <button type="button" class="delete-question-btn delete-btn">x</button>
               <div class="form-control">
                    <label for="question">Question:</label>
                    <input type="text" name="question">
                </div>
                <div class="form-control">
                    <label for="points">Points:</label>
                    <input type="text" name="points" placeholder="1 - 9999">
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

        new Answer(lastQuestion);
    }
}