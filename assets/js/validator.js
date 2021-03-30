export default class Validator{
    static checkStringLength(value, minLength = 3){
        return value.trim().length >= minLength;
    }

    static checkArrayLength(arr, minLength = 3){
        return arr.length >= minLength;
    }

    static checkNumberInput(value){
        const regexp = /^[0-9]{1,4}$/;
        return regexp.test(value.trim()) && value !== "0";
    }

    static checkDateTime(datetime){
        const regexp = /^[0-9]{4}-[0-1][0-9]-[0-3][0-9][A-Z][0-2][0-9]:[0-5][0-9]$/;
        return regexp.test(datetime);
    }

    static validateData(isValid, className, message, input,validation_func){
        const isCorrect = validation_func();
        isValid.add(isCorrect);
        if (!isCorrect && input.parent().find(`p.${className}`).length === 0){
            input.parent().append(`<p class="${className}">${message}</p>`)
        }
        return isCorrect
    }

    static addOnFocusRemoveFormError(input,className){
        input.focus(function (){
            const formError = input.parent().find(`p.${className}`);
            formError.remove();
        })
    }
}