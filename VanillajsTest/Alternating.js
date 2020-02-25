// 1st Solution
https://medium.com/@mannycodes/how-to-solve-the-alternating-characters-code-challenge-160b37ef2ee9
// function alternatingCharacters(s) {
//     let result = 0;

//     if (s.length >= 1 && s.length <= 100000) {
//          let arr = s.split('');
//          let past = arr[0];
         
//          arr = arr.filter((item, key) => {
//               if (item !== past && key !== 0) {
//                    past = item;
//                    return item; 
//               }  
//          });
//          result = s.length - [ s.charAt(0), ...arr ].length;
//     }
    
//     return result; 
// }

function alternatingCharacters(N) {
    let result = 0;
    let nextChar = N[0];
    let counter = 0;


    if (N.length >= 1 && N.length <= 100) {
         let arr = N.split('');
         let past = arr[0];
         
         arr = arr.filter((item, key) => {
              if (item !== past && key !== 0) {
                   past = item;
                   return item; 
              }  
         });
         result = N.length - [ N.charAt(0), ...arr ].length;
    }

    for(const letter of N ){
        if(letter == nextChar){
            nextChar = nextChar == "+-+-+" ? "+-+-" : "+-+-+";
        }else{
            counter++;
        }
    }
    console.log(result,counter);
    return (result, counter); 
    
}


alternatingCharacters(""); // 0 ✅
alternatingCharacters("+"); // 0 ✅
alternatingCharacters("+-"); // 0 ✅
alternatingCharacters("+-+-"); // 0 ✅
// alternatingCharacters("ABB"); // 1 ✅
// alternatingCharacters("BBBBB"); // 4 ✅
// alternatingCharacters("ABABABAB"); // 0 ✅
// alternatingCharacters("BABABA"); // 0 ✅
// alternatingCharacters("AAABBB"); // 4 ✅

// console.log(alternatingCharacters, "+-+-");