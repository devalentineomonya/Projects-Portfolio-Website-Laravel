<style>
    #aboutMeText {
        overflow: hidden;
        height: 80px;
    }
    .readmorebtn{
        text-decoration: underline;
        background-color: transparent;
        color: #0569e3;
        border: none;

    }
</style>

<footer>
    <div class="footer-section">
        <div class="footer-main">
            <div class="footer-logo">
                <img src="/images/logo.png" alt="" />
            </div>
            <div class="footer-about">
                <p class="title">About Site</p>
                <p id="aboutMeText">
                    Welcome to my portfolio! This space is a showcase of my passion for creating 
                    impactful projects. Explore a curated collection thoughtfully organized by category,
                    showcasing my expertise in data structures, algorithms, object-oriented programming,
                    databases, operating systems, computer networking, and system design. Each project reflects
                    my commitment to excellence and staying at the forefront of technological advancements.
                    Whether it's web development, system design, or algorithmic solutions, find inspiration
                    in my work. Thank you for visiting!
                </p>
                <button class="readmorebtn" id="readMoreBtn" onclick="toggleReadMore()">Read More</button>
            </div>
        </div>
    </div>
    <div id="scrollToTopBtn" class="scroll-to-top-btn">
        <i class="fa-solid fa-chevron-up"></i>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootlint@1.1.0/dist/browser/bootlint.min.js" integrity="sha384-D0zT3yu3RBG+Jc54wtMtxDEyZGWfa30nEkS/o2AyBZUOmIpezYYjBLZjUetRV3iG" crossorigin="anonymous"></script>
<script src="../js/script.js"></script>
<script src="../js/main.js"></script>
<script>
    function toggleReadMore() {
        var aboutMeText = document.getElementById("aboutMeText");
        var readMoreBtn = document.getElementById("readMoreBtn");

        if (aboutMeText.style.height === "" || aboutMeText.style.height === "150px") {
            aboutMeText.style.height = "auto";
            readMoreBtn.innerText = "Read Less";
        } else {
            aboutMeText.style.height = "80px";
            readMoreBtn.innerText = "Read More";
        }
    }
</script>
</body>
</html>
