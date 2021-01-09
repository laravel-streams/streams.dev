let mobileMenu;

const toggleMobileMenu = (e) => {
    
    e.preventDefault();
    mobileMenu.classList.toggle('o-mobile-menu--active');

    setTimeout(() => {
        if (mobileMenu.classList.contains('o-mobile-menu--active')) {
            console.log('ok.')
            document.addEventListener('click', hideOnClickOutside)
        }
    }, 0);
    
}

const hideOnClickOutside = (event) => {
    if (!mobileMenu.contains(event.target)) {
        document.removeEventListener('click', hideOnClickOutside)
        mobileMenu.classList.toggle('o-mobile-menu--active');
    } 
}


export default function (selector) {
    
    mobileMenu = document.querySelector('#mobile-menu');
    const toggleButtons = document.querySelectorAll(selector);
    toggleButtons.forEach(btn => {  
        btn.addEventListener('click',toggleMobileMenu)
    });

}