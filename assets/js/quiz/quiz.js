import axios from "axios";
import Validator from "../utils/validator";

export  default class Quiz{
    static CREATE_QUIZ = "create";
    static EDIT_QUIZ = "edit";

    constructor(mode=Quiz.CREATE_QUIZ) {
        switch (mode){
            case Quiz.CREATE_QUIZ:
                $('#quiz-btn').click((event)=>this.createQuiz(event));
                break;
            case Quiz.EDIT_QUIZ:
                $('#quiz-btn').click((event)=>this.updateQuiz(event));
                break;
            default:
                throw new Error("Please choose correct mode for submit button!");

        }

        const inputArr = [$("#title"),$("#description"),$("#expired_at")]
        inputArr.forEach(input=>{
            Validator.addOnFocusRemoveFormError(input,'form-error');
        });

    }

    scrapeData(mode=Quiz.CREATE_QUIZ){
        const isValid = new Set();
        const q = $('#quiz');
        const quizId= q.attr('data-index') ?? null;

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
            const questionId= $(this).attr('data-index') ?? null;

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
                const answerId = $(this).attr('data-index') ?? null;

                const answerInput = $(this).find("input[name='answer']");
                const answer = answerInput.val();
                Validator.validateData(
                    isValid,'form-error',
                    'Too short value! Minimum 6 character is required',
                    answerInput, Validator.checkStringLength.bind(this,answer,1)
                );

                const isCorrect = $(this).find("input[name='isCorrect']").is(':checked');

                answers.push({id:answerId,answer,isCorrect});
            });
            isValid.add(Validator.checkArrayLength(answers,2));
            if (!Validator.checkArrayLength(answers,2)){
                $(this).parent().parent().find('.answers-error').html('<p class="form-error">At least 2 answer must be created for each question!</p>')
            }
            questions.push({id:questionId,question, points:parseInt(points), answers});
        });

        isValid.add(Validator.checkArrayLength(questions));
        // console.log(questions.length)
        if (!Validator.checkArrayLength(questions)){
            $('#question-error').html('<p class="form-error">At least 3 questions must be created!</p>')
        }

        const jsonData = {
            id:quizId,
            title,
            description,
            category,
            expiredAt,
            questions
        };

        return {
            "isValid": !isValid.has(false),
            "jsonData":JSON.stringify(jsonData)
        };
    }

    createQuiz(event){
        event.preventDefault();
        const data = this.scrapeData();
        if (data.isValid){
            axios
                .post("/user/quizzes/create",data.jsonData)
                .then(response=>{
                    //redirect
                    if (response.status === 200){
                        const urlArr = window.location.href.split("/");
                        const hostname = urlArr.slice(0,3).join("/");
                        window.location.href=`${hostname}/user/quizzes`;
                    }
                })
                .catch(error=>console.log(error));
        }
        // console.log(data.isValid);
        // console.log(data.jsonData);
    }

    updateQuiz(event){
        event.preventDefault();
        const data = this.scrapeData(Quiz.EDIT_QUIZ);

        // console.log(data.isValid);
        // console.log(data.jsonData);

        if (data.isValid){
            axios
                .post(`${window.location.pathname}`,data.jsonData)
                .then(response=>{
                    console.log(response);
                    window.location.pathname = '/user/quizzes';
                })
                .catch(error=>console.log(error));
        }
    }
}