class Navbar{
    static addNavbar() {
        $("#navbar_hidden a.bars").click(Navbar.showNavbarHandler);
        $("#overlay").click(Navbar.hideNavbarHandler);
    }

    static hideNavbarHandler(){
        $("#navbar_mobile").css('display','none');
        $("#overlay").css('display','none');
    }

    static showNavbarHandler(){
        const nav = $("#navbar_full");
        nav.removeAttr('id');
        const links = nav.find('li');

        links.each(function(){
            $(this).addClass('mobile_link')
        })
        $("#overlay").show();
        const navbar = $("#navbar_mobile");
        navbar.append(links);
        navbar.show()
    }

}

export default Navbar;