const news = {
  avalon: "4",
  matngu: "18",
};

for (const [key, value] of Object.entries(news)) {
  var a = document.getElementById(`${key}`);
  a.addEventListener("click", () => {
    window.location.href = `/product-detail?id=${value}`;
  });
}

// var a = document.getElementById("avalon");
// a.addEventListener("click", (e) => {
//   window.location.href = "/product-detail?id=4";
// });

// var b = document.getElementById("matngu");
// b.addEventListener("click", (e) => {
//   window.location.href = "/product-detail?id=18";
// });
