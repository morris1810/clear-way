
window.onload = (e) => {
        
    // Check default display mode
    switch (localStorage.getItem("displayMode")) {
        case "light":
            lightMode();
            break;
        case "dark":
            darkMode();
            break;
        default:
            lightMode();
            break;
    }


    // add a onclick listener to all [Switch Display Mode Button]
    const switchDisplayModeBtns = document.querySelectorAll(".switchDisplayModeBtn");        
    
    switchDisplayModeBtns.forEach(btn => {
        btn.addEventListener("click",toggleDarkMode);
    })
    
};

var passwordInputs = document.querySelectorAll(".passwordInput");        

// Some function below
function toggleDarkMode() {
    localStorage.getItem("displayMode") == "light" ? darkMode() : lightMode();
} 

function lightMode() {
    localStorage.setItem("displayMode", "light");
    document.body.classList.remove("darkMode");    

    // Make password input visible
    passwordInputs.forEach(input => {
        input.setAttribute("type", "text");
    })
}


function darkMode() {
    localStorage.setItem("displayMode", "dark");
    document.body.classList.add("darkMode");

    // Make password input invisible
    passwordInputs.forEach(input => {
        input.setAttribute("type", "password");
    })
}