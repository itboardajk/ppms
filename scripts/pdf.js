"use strict";
window.onload = function(){
            document.getElementById("download")
        .addEventListener("click",()=>{
            var apo = document.getElementById("apo");
            console.log(apo);
            console.log(window);
            html2pdf().from(apo).save();
        });
};