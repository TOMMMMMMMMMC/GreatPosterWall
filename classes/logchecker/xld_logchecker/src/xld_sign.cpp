/*
xld_sign.cpp

Homepage: http://tmkk.undo.jp/xld/index_e.html
SVN: http://xld.googlecode.com/svn/trunk/

This is a 32bit app due to the nature of the signing algorithm
so you need 32bit libs  e.g. apt-get install libssl-dev:i386

Compile with -m32

Tools used:
  Ollydbg
  IDA Pro with Hex-Rays decompiler

Notes:
  First of the algo uses a custom IV for it's SHA256
  Then lots of messing around
  And jumping around a base64 table.

  Most the code here is fixed up Hex-Rays dump from the Mac XLDLogChecker.bundle
  The AKA sub_401AA0 refer to the win32 xldlogchecker.exe
*/
#include <cstdlib>
#include <iostream>
#include <sstream>
#include <fstream>
#include <iomanip>
#include <sstream>
#include <string>
#include <cstring>
#include <openssl/sha.h>
#include "defs.h"

const unsigned char custom_sha_ivec[] = {
  0xA4, 0xE3, 0x95, 0x1D, 0xF5, 0x0E, 0x52, 0x06, 0x75, 0xFB, 0x9C,
  0x3A, 0xAE, 0xBC, 0x04, 0x61, 0x82, 0xDA, 0xCE, 0x09, 0x0B, 0xE6,
  0x55, 0xBA, 0xC6, 0x16, 0xEC, 0xEA, 0x15, 0xAF, 0x19, 0xEB
};

// 0040E1A0
const unsigned char temp1[32] = { 0x56, 0xD1, 0x78, 0x3E, 0xC8, 0xB0, 0xB1,
                                  0x39, 0x0B, 0xC6, 0x2B, 0x6A, 0xC8, 0x84,
                                  0xEE, 0xDF, 0x06, 0x81, 0x05, 0x0A, 0x93,
                                  0x92, 0x7B, 0xC5, 0x60, 0xA4, 0x94, 0x74,
                                  0x37, 0x2D, 0x8D, 0xF9 };

// 0040E18C
const unsigned char temp2[8] = {
  0x64, 0x79, 0xB8, 0x73, 0x48, 0x85, 0x3A, 0xFC
};

// 0040E184
const unsigned char temp3[8] = {
  0xC3, 0x33, 0x2D, 0x68, 0xF1, 0x96, 0x8F, 0xB7
};

const unsigned char tab[] =
    "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz._=";

// AKA sub_401AA0
signed int d7w8yhdiuahdushcjkyd7a(unsigned char *a1, const unsigned char *a2) {
  signed int v2; // esi@1

  v2 = -1;
  if (!(a2 == 0 | a1 == 0)) {
    *(_DWORD *)(a1 + 8) = *(_BYTE *)(a2 + 3) | (*(_BYTE *)(a2 + 2) << 8) |
                          (*(_BYTE *)(a2 + 1) << 16) | (*(_BYTE *)a2 << 24);
    *(_DWORD *)(a1 + 12) = *(_BYTE *)(a2 + 7) | (*(_BYTE *)(a2 + 6) << 8) |
                           (*(_BYTE *)(a2 + 5) << 16) |
                           (*(_BYTE *)(a2 + 4) << 24);
    *(_DWORD *)(a1 + 16) = *(_BYTE *)(a2 + 11) | (*(_BYTE *)(a2 + 10) << 8) |
                           (*(_BYTE *)(a2 + 9) << 16) |
                           (*(_BYTE *)(a2 + 8) << 24);
    *(_DWORD *)(a1 + 20) = *(_BYTE *)(a2 + 15) | (*(_BYTE *)(a2 + 14) << 8) |
                           (*(_BYTE *)(a2 + 13) << 16) |
                           (*(_BYTE *)(a2 + 12) << 24);
    *(_DWORD *)(a1 + 24) = *(_BYTE *)(a2 + 19) | (*(_BYTE *)(a2 + 18) << 8) |
                           (*(_BYTE *)(a2 + 17) << 16) |
                           (*(_BYTE *)(a2 + 16) << 24);
    v2 = 0;
    *(_DWORD *)(a1 + 28) = *(_BYTE *)(a2 + 23) | (*(_BYTE *)(a2 + 22) << 8) |
                           (*(_BYTE *)(a2 + 21) << 16) |
                           (*(_BYTE *)(a2 + 20) << 24);
    *(_DWORD *)(a1 + 32) = *(_BYTE *)(a2 + 27) | (*(_BYTE *)(a2 + 26) << 8) |
                           (*(_BYTE *)(a2 + 25) << 16) |
                           (*(_BYTE *)(a2 + 24) << 24);
    *(_DWORD *)(a1 + 36) = *(_BYTE *)(a2 + 31) | (*(_BYTE *)(a2 + 30) << 8) |
                           (*(_BYTE *)(a2 + 29) << 16) |
                           (*(_BYTE *)(a2 + 28) << 24);
  }
  return v2;
}

signed int kljs08ujq2ohnju2782sac(unsigned char *a1, const unsigned char *a2) {
  signed int v2;     // ecx@1
  unsigned char *v3; // edx@2   // WAS INT
  signed int v4;     // ecx@2
  char v5;           // al@3

  v2 = -1;
  if (!(a2 == 0 | a1 == 0)) {
    v3 = a1;
    v4 = 1;
    do {
      v5 = *(_BYTE *)(a2 + v4++ - 1);
      *(_BYTE *)(v3++ + 40) = v5;
    } while (v4 != 9);
    v2 = 0;
  }
  return v2;
}

// AKA sub_405E78
int cznjk2oiqwdjkljaspqwd(const unsigned char *a1, unsigned char *a2,
                          unsigned int a3) {
  _DWORD v3;     // eax@1
  _DWORD v4;     // edx@1
  _DWORD v5;     // edi@1
  _DWORD v6;     // esi@1
  _DWORD v7;     // edi@1
  _DWORD v8;     // esi@1
  _DWORD v9;     // eax@1
  _DWORD v10;    // edi@1
  _DWORD v11;    // esi@1
  _DWORD v12;    // edi@1
  _DWORD v13;    // esi@1
  _DWORD v14;    // edi@1
  _DWORD v15;    // esi@1
  _DWORD v16;    // edi@1
  _DWORD v17;    // esi@1
  _DWORD v18;    // edx@1
  _DWORD v19;    // edi@1
  _DWORD v20;    // esi@1
  _DWORD v21;    // eax@1
  _DWORD v22;    // edx@1
  _DWORD v23;    // edi@1
  _DWORD v24;    // esi@1
  _DWORD v25;    // edi@1
  _DWORD v26;    // esi@1
  _DWORD v27;    // eax@1
  _DWORD v28;    // edi@1
  _DWORD v29;    // esi@1
  _DWORD v30;    // edi@1
  _DWORD v31;    // esi@1
  _DWORD v32;    // edi@1
  _DWORD v33;    // esi@1
  _DWORD v34;    // edi@1
  _DWORD v35;    // esi@1
  _DWORD v36;    // edx@1
  _DWORD v37;    // edi@1
  _DWORD v38;    // esi@1
  _DWORD result; // eax@1

  v3 = _byteswap_ulong(*(_DWORD *)a1);
  v4 = v3 ^ _byteswap_ulong(*(_DWORD *)(a1 + 4));
  v5 = *(_DWORD *)a2 + v4;
  v6 = v5;
  v5 = __ROL__(v5, 1);
  v7 = v6 - 1 + v5;
  v8 = v7;
  v7 = __ROL__(v7, 4);
  v9 = v8 ^ v7 ^ v3;
  *(_DWORD *)a3 = v9;
  v10 = *(_DWORD *)(a2 + 4) + v9;
  v11 = v10;
  v10 = __ROL__(v10, 2);
  v12 = v11 + 1 + v10;
  v13 = v12;
  v12 = __ROL__(v12, 8);
  v14 = *(_DWORD *)(a2 + 8) + (v13 ^ v12);
  v15 = v14;
  v14 = __ROL__(v14, 1);
  v16 = v14 - v15;
  v17 = v16;
  v16 = __ROL__(v16, 16);
  v18 = (v9 | v17) ^ v16 ^ v4;
  *(_DWORD *)(a3 + 4) = v18;
  v19 = *(_DWORD *)(a2 + 12) + v18;
  v20 = v19;
  v19 = __ROL__(v19, 2);
  v21 = (v20 + 1 + v19) ^ v9;
  *(_DWORD *)(a3 + 8) = v21;
  v22 = v21 ^ v18;
  *(_DWORD *)(a3 + 12) = v22;
  v23 = *(_DWORD *)(a2 + 16) + v22;
  v24 = v23;
  v23 = __ROL__(v23, 1);
  v25 = v24 - 1 + v23;
  v26 = v25;
  v25 = __ROL__(v25, 4);
  v27 = v26 ^ v25 ^ v21;
  *(_DWORD *)(a3 + 16) = v27;
  v28 = *(_DWORD *)(a2 + 20) + v27;
  v29 = v28;
  v28 = __ROL__(v28, 2);
  v30 = v29 + 1 + v28;
  v31 = v30;
  v30 = __ROL__(v30, 8);
  v32 = *(_DWORD *)(a2 + 24) + (v31 ^ v30);
  v33 = v32;
  v32 = __ROL__(v32, 1);
  v34 = v32 - v33;
  v35 = v34;
  v34 = __ROL__(v34, 16);
  v36 = (v27 | v35) ^ v34 ^ v22;
  *(_DWORD *)(a3 + 20) = v36;
  v37 = *(_DWORD *)(a2 + 28) + v36;
  v38 = v37;
  v37 = __ROL__(v37, 2);
  result = (v38 + 1 + v37) ^ v27;
  *(_DWORD *)(a3 + 24) = result;
  *(_DWORD *)(a3 + 28) = result ^ v36;
  return result;
}

// AKA sub_401C10
// called with hasyudg8721ebghbgya87(v26, temp3, 0);
// v26 is a buffer being filled
signed int hasyudg8721ebghbgya87(unsigned char *a1, const unsigned char *a2,
                                 unsigned int a3) {
  int v3;            // esi@5
  signed int result; // eax@7
  int v5;            // [sp+1Ch] [bp-3Ch]@5
  int v6;            // [sp+4Ch] [bp-Ch]@5

  if (a2 == 0 | a1 == 0 || *(_DWORD *)(a1 + 48) < a3) {
    result = -1;
  } else {
    if (*(_DWORD *)(a1 + 48) == a3) {
      *(_DWORD *)a1 = (_DWORD)realloc(*(void **)a1, 4 * a3 + 4);
      *(_DWORD *)(a1 + 4) =
          (_DWORD)realloc(*(void **)(a1 + 4), 4 * *(_DWORD *)(a1 + 48) + 4);
      *(_DWORD *)(*(_DWORD *)a1 + 4 * a3) = (_DWORD)malloc(0x20u);
      *(_DWORD *)(*(_DWORD *)(a1 + 4) + 4 * a3) = (_DWORD)malloc(0x80u);
      ++*(_DWORD *)(a1 + 48);
    }
    v3 = 4 * a3;
    // calling sub_405E78
    cznjk2oiqwdjkljaspqwd(a2, a1 + 8, *(_DWORD *)(*(_DWORD *)(a1 + 4 * a3)));
    v5 = 0;
    v6 = 0;
    do {
      *(_DWORD *)(*(_DWORD *)(v3 + *(_DWORD *)(a1 + 4)) + v6) =
          *(_DWORD *)(*(_DWORD *)(v3 + *(_DWORD *)a1) + v5);
      *(_DWORD *)(*(_DWORD *)(v3 + *(_DWORD *)(a1 + 4)) + v6 + 4) =
          *(_DWORD *)(*(_DWORD *)(v3 + *(_DWORD *)a1) + v5);
      *(_DWORD *)(*(_DWORD *)(v3 + *(_DWORD *)(a1 + 4)) + v6 + 8) =
          *(_DWORD *)(*(_DWORD *)(v3 + *(_DWORD *)a1) + v5);
      *(_DWORD *)(v6 + *(_DWORD *)(v3 + *(_DWORD *)(a1 + 4)) + 12) =
          *(_DWORD *)(*(_DWORD *)(v3 + *(_DWORD *)a1) + v5);
      v5 += 4;
      v6 += 16;
    } while (v5 != 32);
    result = 0;
  }
  return result;
}

uint64 __PAIR_U64__(uint32 high, uint32 low) {
  return (((uint64)high) << sizeof(high) * 8) | uint32(low);
}

// AKA sub_405700
int poqwie109ioadiojd2189(unsigned char *sha_buf, _DWORD size, _DWORD is_zero,
                          unsigned char *work_buf_40) {
  unsigned __int64 v4;  // qax@1
  unsigned __int64 v5;  // qax@2
  signed int v6;        // ecx@2
  _DWORD v7;            // edi@3
  _DWORD v8;            // esi@3
  _DWORD v9;            // edi@3
  _DWORD v10;           // esi@3
  _DWORD v11;           // edi@3
  _DWORD v12;           // esi@3
  _DWORD v13;           // edi@3
  _DWORD v14;           // esi@3
  _DWORD v15;           // edi@3
  _DWORD v16;           // esi@3
  _DWORD v17;           // edi@3
  _DWORD v18;           // esi@3
  _DWORD v19;           // edi@3
  _DWORD v20;           // esi@3
  _DWORD v21;           // edi@3
  _DWORD v22;           // esi@3
  _DWORD v23;           // edi@3
  _DWORD v24;           // esi@3
  _DWORD v25;           // edi@3
  _DWORD v26;           // esi@3
  _DWORD v27;           // edi@3
  _DWORD v28;           // esi@3
  _DWORD v29;           // edi@3
  _DWORD v30;           // esi@3
  _DWORD v31;           // edi@3
  _DWORD v32;           // esi@3
  _DWORD v33;           // edi@3
  _DWORD v34;           // esi@3
  unsigned __int64 v35; // qax@4
  signed int v36;       // ecx@7
  _DWORD v37;           // edi@8
  _DWORD v38;           // esi@8
  _DWORD v39;           // edi@8
  _DWORD v40;           // esi@8
  _DWORD v41;           // edi@8
  _DWORD v42;           // esi@8
  _DWORD v43;           // edi@8
  _DWORD v44;           // esi@8
  _DWORD v45;           // edi@8
  _DWORD v46;           // esi@8
  _DWORD v47;           // edi@8
  _DWORD v48;           // esi@8
  _DWORD v49;           // edi@8
  _DWORD v50;           // esi@8
  _DWORD v51;           // edi@8
  _DWORD v52;           // esi@8
  _DWORD v53;           // edi@8
  _DWORD v54;           // esi@8
  _DWORD v55;           // edi@8
  _DWORD v56;           // esi@8
  _DWORD v57;           // edi@8
  _DWORD v58;           // esi@8
  _DWORD v59;           // edi@8
  _DWORD v60;           // esi@8
  _DWORD v61;           // edi@8
  _DWORD v62;           // esi@8
  _DWORD v63;           // edi@8
  _DWORD v64;           // esi@8
  //_DWORD v65; // ebx@9
  _DWORD v66;           // ecx@9
  unsigned __int64 v68; // [sp+0h] [bp-14h]@1

  v4 = _byteswap_uint64(
      __PAIR__(*(_DWORD *)work_buf_40, *(_DWORD *)(work_buf_40 + 4)));
  v68 = v4;
  if (size >= 8) {
    do {
      size -= 8;
      LODWORD(v5) = v68 ^ _byteswap_ulong(*(_DWORD *)sha_buf);
      HIDWORD(v5) = HIDWORD(v68) ^ _byteswap_ulong(*(_DWORD *)(sha_buf + 4));
      v6 = 4;
      do {
        HIDWORD(v5) ^= v5;
        v7 = *(_DWORD *)is_zero + HIDWORD(v5);
        v8 = v7;
        v7 = __ROL__(v7, 1);
        v9 = v8 - 1 + v7;
        v10 = v9;
        v9 = __ROL__(v9, 4);
        LODWORD(v5) = v10 ^ v9 ^ v5;
        v11 = *(_DWORD *)(is_zero + 4) + v5;
        v12 = v11;
        v11 = __ROL__(v11, 2);
        v13 = v12 + 1 + v11;
        v14 = v13;
        v13 = __ROL__(v13, 8);
        v15 = *(_DWORD *)(is_zero + 8) + (v14 ^ v13);
        v16 = v15;
        v15 = __ROL__(v15, 1);
        v17 = v15 - v16;
        v18 = v17;
        v17 = __ROL__(v17, 16);
        HIDWORD(v5) ^= (v5 | v18) ^ v17;
        v19 = *(_DWORD *)(is_zero + 12) + HIDWORD(v5);
        v20 = v19;
        v19 = __ROL__(v19, 2);
        LODWORD(v5) = (v20 + 1 + v19) ^ v5;
        HIDWORD(v5) ^= v5;
        v21 = *(_DWORD *)(is_zero + 16) + HIDWORD(v5);
        v22 = v21;
        v21 = __ROL__(v21, 1);
        v23 = v22 - 1 + v21;
        v24 = v23;
        v23 = __ROL__(v23, 4);
        LODWORD(v5) = v24 ^ v23 ^ v5;
        v25 = *(_DWORD *)(is_zero + 20) + v5;
        v26 = v25;
        v25 = __ROL__(v25, 2);
        v27 = v26 + 1 + v25;
        v28 = v27;
        v27 = __ROL__(v27, 8);
        v29 = *(_DWORD *)(is_zero + 24) + (v28 ^ v27);
        v30 = v29;
        v29 = __ROL__(v29, 1);
        v31 = v29 - v30;
        v32 = v31;
        v31 = __ROL__(v31, 16);
        HIDWORD(v5) ^= (v5 | v32) ^ v31;
        v33 = *(_DWORD *)(is_zero + 28) + HIDWORD(v5);
        v34 = v33;
        v33 = __ROL__(v33, 2);
        LODWORD(v5) = (v34 + 1 + v33) ^ v5;
        --v6;
      } while (v6);
      v68 = v5;
      v35 = _byteswap_uint64(__PAIR_U64__(v5, HIDWORD(v5)));
      *(_QWORD *)sha_buf = v35;
      sha_buf += 8;
    } while (size >= 8);
    v4 = _byteswap_uint64(__PAIR_U64__(v35, HIDWORD(v35)));
  }
  if (size > 0) {
    v36 = 4;
    do {
      HIDWORD(v4) ^= v4;
      v37 = *(_DWORD *)is_zero + HIDWORD(v4);
      v38 = v37;
      v37 = __ROL__(v37, 1);
      v39 = v38 - 1 + v37;
      v40 = v39;
      v39 = __ROL__(v39, 4);
      LODWORD(v4) = v40 ^ v39 ^ v4;
      v41 = *(_DWORD *)(is_zero + 4) + v4;
      v42 = v41;
      v41 = __ROL__(v41, 2);
      v43 = v42 + 1 + v41;
      v44 = v43;
      v43 = __ROL__(v43, 8);
      v45 = *(_DWORD *)(is_zero + 8) + (v44 ^ v43);
      v46 = v45;
      v45 = __ROL__(v45, 1);
      v47 = v45 - v46;
      v48 = v47;
      v47 = __ROL__(v47, 16);
      HIDWORD(v4) ^= (v4 | v48) ^ v47;
      v49 = *(_DWORD *)(is_zero + 12) + HIDWORD(v4);
      v50 = v49;
      v49 = __ROL__(v49, 2);
      LODWORD(v4) = (v50 + 1 + v49) ^ v4;
      HIDWORD(v4) ^= v4;
      v51 = *(_DWORD *)(is_zero + 16) + HIDWORD(v4);
      v52 = v51;
      v51 = __ROL__(v51, 1);
      v53 = v52 - 1 + v51;
      v54 = v53;
      v53 = __ROL__(v53, 4);
      LODWORD(v4) = v54 ^ v53 ^ v4;
      v55 = *(_DWORD *)(is_zero + 20) + v4;
      v56 = v55;
      v55 = __ROL__(v55, 2);
      v57 = v56 + 1 + v55;
      v58 = v57;
      v57 = __ROL__(v57, 8);
      v59 = *(_DWORD *)(is_zero + 24) + (v58 ^ v57);
      v60 = v59;
      v59 = __ROL__(v59, 1);
      v61 = v59 - v60;
      v62 = v61;
      v61 = __ROL__(v61, 16);
      HIDWORD(v4) ^= (v4 | v62) ^ v61;
      v63 = *(_DWORD *)(is_zero + 28) + HIDWORD(v4);
      v64 = v63;
      v63 = __ROL__(v63, 2);
      LODWORD(v4) = (v64 + 1 + v63) ^ v4;
      --v36;
    } while (v36);
    int v65 = (int)sha_buf;
    v66 = size;
    v4 = _byteswap_uint64(__PAIR_U64__(v4, HIDWORD(v4)));
    do {
      *(_BYTE *)v65++ ^= v4;
      v4 >>= 8;
      --v66;
    } while (v66);
  }
  return v4;
}

// AKA sub_401D30
signed int jda89yeq1h139a90dpokdap(unsigned char *work_buf,
                                   unsigned char *sha_buf, _DWORD size,
                                   int is_zero) {
  signed int v4; // ecx@1

  v4 = -1;
  if (!(sha_buf == 0 | work_buf == 0)) {
    poqwie109ioadiojd2189(sha_buf, size,
                          *(_DWORD *)(*(_DWORD *)(work_buf + 4 * is_zero)),
                          work_buf + 40);
    v4 = 0;
  }
  return v4;
}

std::string sha256(const std::string str) {
  unsigned char hash[SHA256_DIGEST_LENGTH];
  SHA256_CTX sha256;
  SHA256_Init(&sha256);
  memcpy(sha256.h, custom_sha_ivec, 32);
  SHA256_Update(&sha256, str.c_str(), str.size());
  SHA256_Final(hash, &sha256);
  std::stringstream ss;
  for (int i = 0; i < SHA256_DIGEST_LENGTH; i++) {
    ss << std::hex << std::setw(2) << std::setfill('0') << (int)hash[i];
  }
  return ss.str();
}

std::string xld_signature(const std::string& str) {
  _BYTE sha_output[128];
  strcpy((char*)sha_output, sha256(str).c_str());

  char sigstring[512]; // [sp+38h] [bp-2CCh]@14

  _BYTE *v4 = (_BYTE *)sha_output;
  _DWORD v6;
  do {
    _DWORD v5 = *(_DWORD *)v4;
    v4 += 4;
    v6 = ~v5 & (v5 - 16843009) & 0x80808080;
  } while (!v6);
  char v7 = (unsigned __int16)(v6 & 0x8080) == 0;
  if (!(v6 & 0x8080))
    v6 = v6 >> 16;
  _BYTE *v8 = v4 + 2;
  if (!v7)
    v8 = v4;
  _BYTE *v9 = v8 - (__CFADD__((_BYTE)v6, (_BYTE)v6) + 3);
  *(_DWORD *)v9 = 1919243786; // could be adding the Version=0001 onto end of
                              // sha string?? dunno
  *(_DWORD *)(v9 + 4) = 1852795251;
  *(_DWORD *)(v9 + 8) = 808464445;
  *(_WORD *)(v9 + 12) = 49;
  _BYTE *v19 = (_BYTE *)sha_output;
  _BYTE *v11;
  _DWORD v12;
  do {
    _DWORD v10 = *(_DWORD *)v19;
    v11 = v19 + 4;
    v19 += 4;
    v12 = ~v10 & (v10 - 16843009) & 0x80808080;
  } while (!v12);
  char v13 = (unsigned __int16)(v12 & 0x8080) == 0;
  if (!(v12 & 0x8080))
    v12 = (unsigned int)v12 >> 16;
  _BYTE *v14 = v11 + 2;
  if (!v13)
    v14 = v11;

  _DWORD v20 = (_DWORD)v14 - (__CFADD__((_BYTE)v12, (_BYTE)v12) + 3) -
               (_DWORD)sha_output;

  unsigned char v26[0x34u];
  memset(v26, 0, 0x34u);
  d7w8yhdiuahdushcjkyd7a(v26, temp1);
  kljs08ujq2ohnju2782sac(v26, temp2);
  hasyudg8721ebghbgya87(v26, temp3, 0);
  jda89yeq1h139a90dpokdap(v26, sha_output, v20, 0);

  //  looks like base64 but is not
  _DWORD v15 = 0;
  _BYTE v22 = 0;
  _DWORD v23 = 0;
  for (_DWORD i = 0; i < v20; ++i) {
    _BYTE v16 = 6 - v15;
    v15 = 8 - (6 - v15);
    sigstring[v23] =
        tab[((unsigned __int8)((signed int)(unsigned __int8) sha_output[i] >>
                               v15) |
             (unsigned __int8)((unsigned __int8)v22 << v16)) &
            0x3F];
    v22 = sha_output[i];
    if (v15 == 6) {
      v15 = 0;
      sigstring[v23 + 1] = tab[v22 & 0x3F];
      v23 += 2;
    } else {
      ++v23;
    }
  }
  if (v15)
    sigstring[v23++] = tab[((unsigned __int8)v22 << (6 - v15)) & 0x3F];
  sigstring[v23] = 0; // null terminate
  std::ostringstream s;
  s << "-----BEGIN XLD SIGNATURE-----\n"
    << sigstring
    << "\n-----END XLD SIGNATURE-----\n";
  return s.str();
}

int main(int argc, char *argv[]) {
  if ((argc != 2) && (argc != 3)) {
    std::cout << "Usage: " << argv[0] << "[-v] [-l] <logfile>\n"
              << "  -l  dumps full log plus signature to STDOUT\n"
              << "  -v  verify log\n";
    return 1;
  }

  const char* file_name = argv[argc-1];
  std::ifstream in(file_name, std::ifstream::binary);
  if (!in.is_open()) {
    std::cerr << "ERROR: Unable to open log \"" << file_name << "\"\n";
    return -1;
  }

  std::ostringstream ss;
  ss << in.rdbuf();
  std::string log(ss.str());

  if (argc == 3 && (strcmp("-v", argv[1])==0)) {
    if ((log.size() <= 0x19) || (log.size() > 0x7A120) ||
        (strncmp(log.c_str(), "X Lossless Decoder version", 26))) {
      std::cout << file_name << ": Not a logfile\n";
      return -2;
    }

    size_t sig_start = log.find("\n-----BEGIN XLD SIGNATURE-----\n");
    size_t sig_end = log.find("\n-----END XLD SIGNATURE-----\n");

    if ((sig_start == std::string::npos) || (sig_end == std::string::npos)) {
      std::cout << file_name << ": Not signed\n";
      return -3;
    }
    if ((sig_end + 29 != log.size()) || (sig_end - sig_start - 31 > 0x1FF) ) {
      std::cout << file_name << ": Malformed\n";
      return -3;
    }

    std::string plain_log(log.begin(), log.begin() + sig_start);
    std::string sig_real = xld_signature(plain_log);
    std::string sig = log.substr(sig_start + 1);

    if (sig != sig_real) {
      std::cout << file_name << ": Bad Sig\n";
      return -3;
    }

    std::cout << file_name << ": OK\n";
    return 0;
  }

  std::cout << xld_signature(log);
  return 0;
}

