import Validator from "./validator";

export default class Answer{
    static  maxLength = 6;

    constructor(question) {
        question.find('button.add-answer-btn').click(this.addAnswer.bind(this,question));
    }

    addAnswer(question){
        // create answer div
        const answerDiv =`<div class="answer">
            <button type="button" class="delete-answer-btn delete-btn">x</button>
            <div class="form-control">
                <label for="answer">Answer: </label>
                <input type="text" name="answer">
            </div>
            <div class="form-control display-inline-block">
                <label for="isCorrect">Answer is correct: </label>
                <input type="checkbox" name="isCorrect">
            </div>
        </div>`;
        const answers = question.find('.answers');
        answers.append(answerDiv);

        // reset form error for answer
        const lastAnswer = answers.find('.answer').last();
        Validator.addOnFocusRemoveFormError(lastAnswer.find('input[name="answer"]'),'form-error');
        lastAnswer.parent().parent().find('.answers-error').html('');

        // add delete btn handler
        this.addDeleteBtnHandler(lastAnswer);
        if(this.maxLength <= answers.find('.answer').length){
            question.find('button.add-answer-btn').addClass('hidden');
        }

    }

    addDeleteBtnHandler(answer){
        const btn = answer.find('button.delete-answer-btn');
        btn.click(this.deleteBtnHandler.bind(this,answer));
    }

    deleteBtnHandler(answer){
        const answers=answer.parent().find('.answer');
        if(Answer.maxLength <= answers.length){
            answer.parent().parent().find('.add-answer-btn').removeClass('hidden');
        }
        answer.parent().parent().find('.answers-error').html('');
        answer.remove();
    }

}