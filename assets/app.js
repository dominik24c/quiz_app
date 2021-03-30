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

class App{
    constructor() {
        this.question = null;
        this.quiz = null;
    }
    init(){
        // console.log(window.location.pathname);
        if(window.location.pathname==="/user/quizzes/create"){
            this.question = new Question();
            this.quiz = new Quiz();
        }

    }
}

const app = new App();
app.init();