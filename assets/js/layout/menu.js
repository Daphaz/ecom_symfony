const burger = document.getElementById("burger");
const menu = document.getElementById("mobile");
const closeMenu = document.getElementById("closeMenu");
const itemsMobile = document.querySelectorAll(".item_mobile");

burger.addEventListener("click", () => {
	menu.classList.toggle("active");
	document.body.classList.toggle("menu");
});

closeMenu.addEventListener("click", () => {
	menu.classList.remove("active");
	document.body.classList.remove("menu");
});

for (let item of itemsMobile) {
	item.addEventListener("click", () => {
		menu.classList.remove("active");
		document.body.classList.remove("menu");
	});
}
