import axios from "axios";
import Validator from "./validator";

export  default class Quiz{
    constructor() {
        $('#create-quiz-btn').click((event)=>this.createQuiz(event));
        const inputArr = [$("#title"),$("#description"),$("#expired_at")]
        inputArr.forEach(input=>{
            Validator.addOnFocusRemoveFormError(input,'form-error');
        });

    }

    scrapeData(){
        const isValid = new Set();
        const q = $('#quiz');

        const titleInput = q.find('#title');
        const title = titleInput.val();
        Validator.validateData(
            isValid,'form-error',
            'Too short value! Minimum 6 character is required',
            titleInput, Validator.checkStringLength.bind(this,title,6)
        );

        const descriptionInput = q.find('#description');
        const description = descriptionInput.val();
        Validator.validateData(
            isValid,'form-error',
            'Too short value! Minimum 20 character is required',
            descriptionInput, Validator.checkStringLength.bind(this,description,20)
        );

        const category = q.find('#category').val();

        const expiredAtInput = q.find('#expired_at');
        const expiredAt = expiredAtInput.val();
        Validator.validateData(
            isValid,'form-error', 'Please Enter the date!',
            expiredAtInput,Validator.checkDateTime.bind(this,expiredAt)
        );

        const questions = [];
        const questionsDivs = q.find('.question');

        questionsDivs.each(function (){
            const questionInput = $(this).find("input[name='question']");
            const question = questionInput.val();
            Validator.validateData(
                isValid,'form-error',
                'Too short value! Minimum 5 character is required',
                questionInput, Validator.checkStringLength.bind(this,question,5)
            );

            const pointsInput =$(this).find("input[name='points']");
            const points = pointsInput.val();
            Validator.validateData(
                isValid,'form-error',
                'Please enter number 1 - 9999',
                pointsInput, Validator.checkNumberInput.bind(this,points)
            )


            const answers = [];
            $(this).find('.answer').each(function (){
                const answerInput = $(this).find("input[name='answer']");
                const answer = answerInput.val();
                Validator.validateData(
                    isValid,'form-error',
                    'Too short value! Minimum 6 character is required',
                    answerInput, Validator.checkStringLength.bind(this,answer,1)
                );

                const isCorrect = $(this).find("input[name='isCorrect']").is(':checked');
                answers.push({answer,isCorrect});
            });
            isValid.add(Validator.checkArrayLength(answers,2));
            if (!Validator.checkArrayLength(answers,2)){
                $(this).parent().parent().find('.answers-error').html('<p class="form-error">At least 2 answer must be created for each question!</p>')
            }
            questions.push({question,points,answers});
        });

        isValid.add(Validator.checkArrayLength(questions));
        console.log(questions.length)
        if (!Validator.checkArrayLength(questions)){
            $('#question-error').html('<p class="form-error">At least 3 questions must be created!</p>')
        }

        const jsonData = JSON.stringify({
            title,
            description,
            category,
            expiredAt,
            questions
        });
        return {
            "isValid": !isValid.has(false),
            jsonData
        };
    }
    createQuiz(event){
        event.preventDefault();
        const data = this.scrapeData();
        if (data.isValid){
            axios
                .post("/user/quizzes/create",data.jsonData)
                .then(res=>console.log(res))
                .catch(err=>console.log(err));
        }
        console.log(data.isValid);
        console.log(data.jsonData);
    }
}