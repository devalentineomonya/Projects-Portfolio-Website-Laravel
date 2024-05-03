// document.addEventListener("DOMContentLoaded", function () {
//     var topNav = document.querySelector('.navbar');

//     window.addEventListener('scroll', function () {
//         if (window.scrollY > 0) {
//             topNav.classList.add('sticky-nav', 'sticky-nav-background');
//             topNav.classList.remove('navbar');
//         } else {
//             topNav.classList.remove('sticky-nav', 'sticky-nav-background');
//             topNav.classList.add('navbar');
//         }
//     });
// });

document.addEventListener("DOMContentLoaded", function () {
    var scrollToTopBtn = document.getElementById("scrollToTopBtn");

    // Show or hide the button based on scroll position
    window.onscroll = function () {
        if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
            scrollToTopBtn.style.display = "block";
        } else {
            scrollToTopBtn.style.display = "none";
        }
    };

    // Scroll to the top when the button is clicked
    scrollToTopBtn.addEventListener("click", function () {
        document.body.scrollTop = 0;
        document.documentElement.scrollTop = 0;
    });
});
