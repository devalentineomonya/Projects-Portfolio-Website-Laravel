
   var anchor = document.querySelectorAll("a[rel=page]");
   [].slice.call(anchor).forEach(function (trigger) {
       trigger.addEventListener("click", function (e) {
           e.preventDefault(); 

           var pageUrl = this.getAttribute("href");

           ajax(pageUrl, function (data) {
               document.querySelector("#load").innerHTML = data;
           });

           if (pageUrl !== window.location) {
               window.history.pushState({ url: pageUrl }, '', pageUrl);
           }
           return false;
       });
   });

   window.addEventListener("popstate", function () {
       ajax(this.window.location.pathname, function (data) {
           document.querySelector("#load").innerHTML = data;
       });
   });

    const mobileNavToggleBtn = document.querySelector('.mobile-nav-toggle');
  
    function mobileNavToogle() {
      document.querySelector('body').classList.toggle('mobile-nav-active');
      mobileNavToggleBtn.classList.toggle('bi-list');
      mobileNavToggleBtn.classList.toggle('bi-x');
    }
    mobileNavToggleBtn.addEventListener('click', mobileNavToogle);
  
    /**
     * Hide mobile nav on same-page/hash links
     */
    document.querySelectorAll('#navmenu a').forEach(navmenu => {
      navmenu.addEventListener('click', () => {
        if (document.querySelector('.mobile-nav-active')) {
          mobileNavToogle();
        }
      });
  
    });
  
