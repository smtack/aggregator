const menuButton = document.querySelector('.toggle-menu');
const menu = document.querySelector('.menu');

const toggleUsers = document.querySelector('#toggle-user-results');
const togglePosts = document.querySelector('#toggle-post-results');
const toggleCategories = document.querySelector('#toggle-category-results');
const userResults = document.querySelector('.user-results');
const postResults = document.querySelector('.post-results');
const categoryResults = document.querySelector('.category-results');

const flash = document.querySelector('.flash');
const close = document.querySelector('.close');

window.onload = () => {
  if(menu) {
    menu.style.display = "none";
  }

  if(toggleUsers) {
    userResults.style.display = "none";
    categoryResults.style.display = "none";
    togglePosts.style.borderBottom = "3px solid #2d55ff";
  }
}

if(menuButton) {
  menuButton.addEventListener("click", () => {
    if(menu.style.display == "none") {
      menu.style.display = "block";
    } else {
      menu.style.display = "none"
    }
  })
}

if(toggleUsers) {
  toggleUsers.addEventListener('click', () => {
    userResults.style.display = "block";
    postResults.style.display = "none";
    categoryResults.style.display = "none";
    togglePosts.style.borderBottom = "none";
    toggleCategories.style.borderBottom = "none";
    toggleUsers.style.borderBottom = "3px solid #2d55ff";
  })

  togglePosts.addEventListener('click', () => {
    userResults.style.display = "none";
    postResults.style.display = "block";
    categoryResults.style.display = "none";
    togglePosts.style.borderBottom = "3px solid #2d55ff";
    toggleUsers.style.borderBottom = "none";
    toggleCategories.style.borderBottom = "none";
  })

  toggleCategories.addEventListener('click', () => {
    userResults.style.display = "none";
    postResults.style.display = "none";
    categoryResults.style.display = "block";
    toggleCategories.style.borderBottom = "3px solid #2d55ff";
    toggleUsers.style.borderBottom = "none";
    togglePosts.style.borderBottom = "none";
  })

  toggleUsers.addEventListener('hover', () => {
    toggleUsers.style.borderBottom = "3px solid #2d55ff";
  })

  togglePosts.addEventListener('hover', () => {
    togglePosts.style.borderBottom = "3px solid #2d55ff";
  })

  toggleCategories.addEventListener('hover', () => {
    toggleCategories.style.borderBottom = "3px solid #2d55ff";
  })
}

if(flash) {
  close.addEventListener('click', () => {
    flash.style.display = "none";
  })
}