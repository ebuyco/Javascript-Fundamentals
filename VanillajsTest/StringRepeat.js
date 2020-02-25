function repeatStringNumTimes(str, num) {
    //Declare an empty array variable to hold the new string.
    // var array = [];
    const array = [];
  
    /*Use a for loop to iterate through the string the given
      number of times.*/
    for(i = 0; i < num;) { 
  
    /*Set the str variable to the array. The array will
      contain the given string n amount of times (n) being
      the number given to the num parameter in the function.*/
      array[i++] = str;
      
      //Return the array with the new string and join it together with no spaces.
    } return array.join("");
  }
  
  repeatStringNumTimes("abc", 3);

console.log(repeatStringNumTimes);

const repeatStr = (n, s) =>  {
    let i  = s.repeat(n) 
      return i;
    }

    console.log(repeatStr)


    const repeatStringNumTimes1 = (str, num) =>  {
        // repeat after me
      let empty="";
      let text=[];
      if(num > 0){
        for(let i=0; i < num;  i++){
          text+=str;
        }
      return text;
      }else{
      console.log(text);
      return empty;
      } 
      }

      repeatStringNumTimes1("abc", -2);

      const firstNotRepeatingCharacter = (s) => {

        for(let i = 0 ; i < s.length; i++){
            let str = s[i]
            if(!s.replace(s[i]," ").includes(str)){
                return s[i]
            }
    
        }
       return '_'
    }
    
    console.log(firstNotRepeatingCharacter("abacabad"))
    console.log(firstNotRepeatingCharacter("abacabaabacaba"))
    console.log(firstNotRepeatingCharacter("abcdefghijklmnopqrstuvwxyziflskecznslkjfabe"))


  const alternatingCharacters = (s) => {
        let result = 0;
    
        if (s.length >= 1 && s.length <= 100000) {
             let arr = s.split('');
             let past = arr[0];
             
             arr = arr.filter((item, key) => {
                  if (item !== past && key !== 0) {
                       past = item;
                       return item; 
                  }  
             });
             result = s.length - [ s.charAt(0), ...arr ].length;
        }
        
        return result; 
   }

console.log(alternatingCharacters);