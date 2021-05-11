import axios from "axios";

export default class SolutionOfQuiz{
    constructor(quizId, questions) {
            this.quizId = parseInt(quizId);
            this.questions = questions;
            this.currentIndex = 0;
            this.answers = [];
            this.solutionIsSended = false;

            this.nextQuestion();
    }


    static addStartQuizClickHandler(){
        const pathname = window.location.pathname.split("/");
        const quizId = parseInt(pathname[2]);

        if(!isNaN(quizId)){
            $('#start-quiz-btn').click(SolutionOfQuiz.fetchQuestions.bind(this,quizId));
        }
    }

    static fetchQuestions(quizId){
        axios.get(`/quiz/${quizId}/get-questions`)
            .then(response=>{
                const data = response.data;
                new SolutionOfQuiz(quizId,[...data]);
            })
            .catch(error=>console.log(error));
    }

    createQuestionForm(index,question,answers, isLastQuestion = false){
        let answersDiv = '';
        let btn = '';

        for(const a of answers){
            answersDiv += `
                <input data-answer_id=${a.id} type="checkbox" name="${a.answer}" value="${a.answer}"> ${a.answer} <br/>
        `;
        }

        let btnText = 'Next';
        if(isLastQuestion) {
            btnText = 'Send your solution';
        }
        btn = `<button type="button" class="button">${btnText}</button>`;

        const questionDiv=`
            <div id="question">
                <p data-question_id="${question.id}">${index+1}. ${question.question} </p>
                ${answersDiv}
                ${btn}
            </div>
        `;

        const descriptionOfQuiz = $('#description');

        if(descriptionOfQuiz){
            descriptionOfQuiz.remove();
        }

        const questionsDiv = $('#questions');
        questionsDiv.html(questionDiv);

        if(isLastQuestion){
            questionsDiv.find('#question button').click(this.sendAnswersBtnHandler.bind(this));
        }else{
            questionsDiv.find('#question button').click(this.nextQuestionBtnHandler.bind(this));
        }
    }

    nextQuestion(isLastQuestion=false){
        this.createQuestionForm(this.currentIndex,{
                id: this.questions[this.currentIndex].id,
                question: this.questions[this.currentIndex].question
            }, [...this.questions[this.currentIndex].answers],
            isLastQuestion
        );
    }

    addAnswer(){
        const answersId = []
        $('#question').find('input').each(function (){
            if($(this).is(':checked')){
                answersId.push({id: parseInt($(this).attr('data-answer_id'))})
            }
        });

        // const questionId = $('#question p').attr('data-question_id');
        // this.answers.push({id:parseInt(questionId),answers: answersId});
        this.answers = [...this.answers, ...answersId]
    }

    nextQuestionBtnHandler(){
        this.addAnswer();
        this.currentIndex++;

        let isLastQuestion = false;
        if(this.questions.length-1 <= this.currentIndex){
            isLastQuestion = true
        }
        this.nextQuestion(isLastQuestion);

    }

    sendAnswersBtnHandler(){
        if(!this.solutionIsSended){
            this.solutionIsSended = true;
            if(this.questions.length-1 <= this.currentIndex){
                this.addAnswer();
            }

            axios.post(`/quiz/${this.quizId}/solve`,this.answers)
                .then(response=>{
                    console.dir(response.data);
                    //redirect
                    window.location.pathname =  `quiz`;
                })
                .catch(error=>{
                    console.log(error);
                    this.solutionIsSended = false;
                });
        }

    }


}