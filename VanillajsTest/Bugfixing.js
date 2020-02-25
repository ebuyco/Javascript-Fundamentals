function solution(A) {
    for (i = 1; i < 1000000; i++) {
      if(!A.includes(i)) return i;
    }
  }

  console.log(solution);