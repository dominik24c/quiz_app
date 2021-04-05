export default class UserQuizzes{
    static DELETE = 'delete';
    static EDIT = 'edit';

    static addEditAndDeleteBtnEventHandler(){
        const listOfQuizzesActions = $('.quiz .quiz-actions');
        console.dir(listOfQuizzesActions)
        listOfQuizzesActions.each(function(){
            const quizId = $(this).attr('id');
            const id = quizId.substr('quiz_'.length,quizId.length);
            $(this).find('button.delete-quiz-btn').click(UserQuizzes.doAction.bind(this, UserQuizzes.EDIT, id))
            $(this).find('button.delete-quiz-btn').click(UserQuizzes.doAction.bind(this, UserQuizzes.DELETE, id))
        });
    }

    static doAction(actionName, id){
        window.location.pathname = `/user/quizzes/${id}/${actionName}`;
    }

}