export default class Quizzes{
    static DELETE = 'delete';
    static EDIT = 'edit';
    static SOLVE = 'solve'

    static addEditAndDeleteBtnClickHandler(){
        $('.quiz .quiz-actions').each(function(){
            const id = Quizzes.getIdFromQuizDiv($(this));
            $(this).find('button.edit-btn').click(Quizzes.doAction.bind(this, '/user/quizzes', Quizzes.EDIT, id))
            $(this).find('button.delete-quiz-btn').click(Quizzes.doAction.bind(this,'/user/quizzes', Quizzes.DELETE, id))
        });
    }

    static addSolveBtnClickHandler(){
        $('.quiz .quiz-actions').each(function(){
            const id = Quizzes.getIdFromQuizDiv($(this));
            $(this).find('button.solve-btn').click(Quizzes.doAction.bind(this, '/quiz', Quizzes.SOLVE, id))
        });
    }

    static getIdFromQuizDiv(quizObj){
        const quizId = quizObj.attr('id');
        return quizId.substr('quiz_'.length, quizId.length);
    }

    static doAction(pathname, actionName, id){
        window.location.pathname = `${pathname}/${id}/${actionName}`;
    }

}