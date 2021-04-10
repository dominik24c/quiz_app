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
import Quizzes from "./js/quizzes";
import SearchInput from "./js/search-input";
import SolutionOfQuiz from "./js/solution-of-quiz";

class App{
    constructor() {
        this.question = null;
        this.quiz = null;
        this.solutionOfQuiz = null;
    }

    init(){
        const edit_route_regex = /^\/user\/quizzes\/.+\/edit$/;
        const solve_quiz_route_regex = /^\/quiz\/.+\/solve$/;
        // console.log(edit_route_regex.test(window.location.pathname));

        // Create quiz page
        if(window.location.pathname === "/user/quizzes/create" ){
            this.question = new Question();
            this.quiz = new Quiz();
        }
        // Edit quiz page
        else if(edit_route_regex.test(window.location.pathname)){
            this.question = new Question();
            this.quiz = new Quiz(Quiz.EDIT_QUIZ);
            this.question.getQuestions();
        }
        // List of user quizzes
        else if(window.location.pathname === "/user/quizzes"){
            Quizzes.addEditAndDeleteBtnClickHandler()
        }
        //Quizzes page
        else if(window.location.pathname === "/quiz"){
            const searchInput = new SearchInput();
            searchInput.addClickEventHandler();
            Quizzes.addSolveBtnClickHandler()
        }
        //Solve Quiz page
        else if(solve_quiz_route_regex.test(window.location.pathname)){
            SolutionOfQuiz.addStartQuizClickHandler();
        }
        //remove query param crate_quiz
        const urlPath = window.location.protocol + "//" + window.location.host + window.location.pathname;
        window.history.pushState({ path: urlPath }, '', urlPath);

    }
}

// run application
const app = new App();
app.init();