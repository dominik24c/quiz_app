export default class SearchInput{
    constructor() {
        this.searchDiv = $('#search-input');
        this.searchBtn = null;
    }

    addClickEventHandler(){
        if(this.searchDiv){
            this.searchBtn = this.searchDiv.find('#search-btn');
            const searchInput = this.searchDiv.find('input[type="text"');

            this.searchBtn.click(this.searchClickHandler.bind(this,searchInput));
        }

    }

    searchClickHandler(searchInput){
        const searchedTitle = searchInput.val();
        window.location.search = `?search=${searchedTitle}`;
    }
}