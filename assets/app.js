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


import Question from "./js/quiz/question";
import Quiz from "./js/quiz/quiz";
import Quizzes from "./js/quiz/quizzes";
import SearchInput from "./js/utils/search-input";
import SolutionOfQuiz from "./js/quiz/solution-of-quiz";
import Navbar from './js/utils/navbar';

class App{
    constructor() {
        this.question = null;
        this.quiz = null;
    }

    init(){
        //rwd navbar
        Navbar.addNavbar();

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

    }
}

// run application
const app = new App();
app.init();