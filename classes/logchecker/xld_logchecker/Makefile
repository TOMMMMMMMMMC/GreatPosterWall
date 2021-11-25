RELEASE_DIR=.
DEBUG_DIR=./debug
SRC_DIR=./src

#CC = g++
CC=clang++-3.5

CFLAGS_DEBUG = -m32 -static -O0 -g3 -I/usr/include/c++/4.9
CFLAGS_RELEASE = -m32 -static -O3 -I/usr/include/c++/4.9
CLIBS = -lcrypto

CINC = -I$(SRC_DIR)

CPP_FILES = $(wildcard src/*.cpp)
RELEASE_FILES = $(addprefix $(RELEASE_DIR)/,$(notdir $(CPP_FILES:.cpp=.out)))
DEBUG_FILES = $(addprefix $(DEBUG_DIR)/,$(notdir $(CPP_FILES:.cpp=.out)))

release: $(RELEASE_FILES) Makefile
debug: $(DEBUG_FILES) Makefile

all: debug release

$(RELEASE_DIR)/%.out: $(SRC_DIR)/%.cpp Makefile
	$(CC) $(CFLAGS_RELEASE) $(CWARN_ALL) $(CINC) $< -o $(RELEASE_DIR)/$(notdir $@) $(CLIBS)

$(DEBUG_DIR)/%.out: $(SRC_DIR)/%.cpp Makefile
	$(CC) $(CFLAGS_DEBUG) $(CWARN_ALL) $(CINC) $< -o $(DEBUG_DIR)/$(notdir $@) $(CLIBS)

clean:
	rm $(RELEASE_DIR)/*.out
	rm $(DEBUG_DIR)/*.out

