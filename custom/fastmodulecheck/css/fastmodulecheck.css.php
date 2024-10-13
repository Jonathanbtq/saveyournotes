<?php
// Informer le navigateur que le fichier doit être interprété comme du CSS
header("Content-Type: text/css");
?>

::root {
    --colorbackhmenu1: rgb(38,60,92);
}
#hiddenTable_fastmodulecheck tbody {
    display: flex;
    flex-direction: column;
    width: 100%;
}

#toggleButton {
    width: 20px;
    height: 20px;
    background-color: var(--colorbackhmenu1); /* Vert clair par défaut */
    color: white;
    border: none;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    cursor: pointer;

    border-radius: 12px;
    position: relative;
}
#toggleButton::after {
  content: "\25BC"; /* Flèche vers le bas */
  position: absolute;
  top: 50%;
  right: 3px;
  transform: translateY(-50%);
  font-size: 14px;
}
#toggleButton:hover {
  background-color: #8d8a9a; /* Vert un peu plus foncé */
}

#hiddenTable_fastmodulecheck {
    display: flex;
    position: absolute;
    min-width: 15vw;
    max-width: 20vw;
    height: 35vw;
    top: 100%;
    z-index: 4000;
    border: 2px solid white;
    padding : 10px;

    background-color: #fdfdfd;
    border-radius: 10px;
    box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
    justify-content: center;
    overflow: scroll;
    overflow-x: hidden;
}
#hiddenTable_fastmodulecheck.ntchild[1] {
    display: flex;
    width: 100%;
}
.table_fast_title th {
    background-color: #fdfdfd;
    padding: 10px;
    text-align: left;
}
.table_fast_title {
    display: flex;
    flex-wrap: no-wrap;
    justify-content: space-around;
    border-bottom: 1px #d8d8d8 solid;
}
.table_fast_value {
    display: flex;
    width: 100%;
    flex-wrap: nowrap;
    align-items: center;
}
.table_fast_value td {
    display: flex;
    justify-content: center;
    width: 50%;
    text-align: start;
}

.table_fast_value td.fast_summary {
	color: #999;
	text-align: center;
	width: 100%;
}

.table_fast_value a.reposition {
    display: inline-block;
    <!-- background-color: #3498db; -->
    color: white;
    border-radius: 5px;
    text-decoration: none;
}

.table_fast_value a.reposition.reload {
	padding-top: 7px;
	color: var(--colortextlink);
}
.table_fast_value a.reposition:hover {
    background-color: #d6d6d6;
}
div.login_block {
    display: flex !important;
    align-items: center !important;
}
div.login_block_other {
    display: flex !important;
    max-width: 500px !important;
    justify-content: center !important;
    align-items: center;
}
