cd "C:\Program Files (x86)\KISS Platform 5.1.2\KISS" && "C:\Program Files (x86)\KISS Platform 5.1.2\MinGW\bin\gcc.exe" -std=c99 -Wall -I"C:\Program Files (x86)\KISS Platform 5.1.2\KISS\prefix\usr\include" -include stdio.h -include kovan/kovan.h -include kovan/kovan.h -L"C:\Program Files (x86)\KISS Platform 5.1.2\KISS\prefix\usr\lib" -lkovan -o %1 %2 2>&1