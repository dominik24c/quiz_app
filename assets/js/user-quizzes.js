export default class UserQuizzes{
    static addEditAndDeleteBtnEventHandler(){
        const listOfQuizzesActions = $('.quiz .quiz-actions');
        console.dir(listOfQuizzesActions)
        listOfQuizzesActions.each(function(){
            const quizId = $(this).attr('id');
            const id = quizId.substr('quiz_'.length,quizId.length);
            $(this).find('button.edit-btn').click(function (){
                window.location.pathname = `/user/quizzes/${id}/edit`;
            })
        });
    }
}