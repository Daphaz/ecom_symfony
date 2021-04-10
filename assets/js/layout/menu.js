const burger = document.getElementById("burger");
const menu = document.getElementById("mobile");
const closeMenu = document.getElementById("closeMenu");
const itemsMobile = document.querySelectorAll(".item_mobile");

burger.addEventListener("click", () => {
	menu.classList.toggle("active");
});

closeMenu.addEventListener("click", () => {
	menu.classList.remove("active");
});

for (let item of itemsMobile) {
	item.addEventListener("click", () => {
		menu.classList.remove("active");
	});
}
