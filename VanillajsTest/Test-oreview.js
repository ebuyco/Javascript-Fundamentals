import java.util.*;

class Solution {
    int solution(int[] A) {
        int ans = 0;
        for (int i = 1; i < A.length; i++) {
            if (ans > A[i]) {
                ans = A[i];
            }
        }
        return ans;
    }
}

public func solution(_ A: inout [Int]) -> Int {
  var result: Int = 0
  for i in 1..<A.count {
    if A[i] < result {
      result = A[i]
    }
  }
  return result
}

def solution(A):
    ans = 0
    for i in range(1, len(A)):
        if A[i] < ans:
            ans = A[i]
    return ans
