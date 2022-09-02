#!/usr/bin/env bash

STANDARD=""
RED=""
YELLOW=""
GREEN=""
BACKGROUND_RED=""
SPACE=""
EXTEND_TO_END_OF_LINE=""
RESET=""
STATUS_OK="✔"
STATUS_ERROR="✘"
STATUS_WARNING="?"

# Deal with windows
if [[ ( -t 1 || "$(tty)" == "CON") && ${TERM} != "winansi" && ${TERM} != "dumb" ]] ; then
    STANDARD="\033[1;0m"
    RED="\033[0;31m"
    YELLOW="\033[0;33m"
    GREEN="\033[1;32m"
    BACKGROUND_RED="\033[41m"
    SPACE="    "
    EXTEND_TO_END_OF_LINE="\033[K"
    RESET="\033[0m"

    read -r -d '' BEAVER << EOF
${STANDARD}                 ___
              .="   "=._.---.
            ."         c ' Y'\`p
           /   ,       \`.  w_/
    Castor |   '-.   /     /
     _,..._|      )_-\\ \\_=.\\
    \`-....-'\`------)))\`=-'"\`'"
        Pre-commit hook
EOF

    echo -e "\n${BEAVER}${RESET}\n";
fi

if [ "$(tty)" == "CON" ] ; then
    STATUS_OK="OK:"
    STATUS_ERROR="ERR:"
fi

print_header () {
    echo -e "\n${SPACE}${STANDARD}${1}...${RESET}"
}

print_success () {
    echo -e "\n${SPACE}${GREEN}${STATUS_OK} ${1}${GREEN}${RESET}"
}

print_failure () {
    echo -e "\n${SPACE}${RED}${STATUS_ERROR} ${1}${RED}${RESET}"
}

print_warning () {
    echo -e "\n${SPACE}${YELLOW}${STATUS_WARNING} ${1}${YELLOW}${RESET}"
}

print_program_error_multi_line () {
    while read -r line; do
        echo -e "${SPACE}  ${BACKGROUND_RED}${STANDARD}${line}${EXTEND_TO_END_OF_LINE}${RESET}";
    done <<< "${1}"
}
