/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

// start the Stimulus application
import './bootstrap';

import Question from "./js/question";
import Quiz from "./js/quiz";
import UserQuizzes from "./js/user-quizzes";

class App{
    constructor() {
        this.question = null;
        this.quiz = null;
    }

    init(){
        const edit_route_regex = /^\/user\/quizzes\/.+\/edit$/;
        console.log(edit_route_regex.test(window.location.pathname))
        if(window.location.pathname === "/user/quizzes/create" ){
            this.question = new Question();
            this.quiz = new Quiz();
        }
        else if(edit_route_regex.test(window.location.pathname)){
            this.question = new Question();
            this.quiz = new Quiz(Quiz.EDIT_QUIZ);
            this.question.getQuestions();
        }
        else if(window.location.pathname === "/user/quizzes"){
            UserQuizzes.addEditAndDeleteBtnEventHandler()
        }
        //remove query param crate_quiz
        const urlPath = window.location.protocol + "//" + window.location.host + window.location.pathname;
        window.history.pushState({ path: urlPath }, '', urlPath);
    }
}

const app = new App();
app.init();