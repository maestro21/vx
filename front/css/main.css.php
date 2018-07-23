.container {
	max-width: 1200px;
}

nav {
	position: fixed !important;
    z-index: 100;
	box-shadow: none !important;
}

nav .tabs__bar {
	background:transparent !important;
}

nav.drawer {
	max-width:calc(100vw - 300px);
}

 .tabs__bar,
.application,
nav,
footer {
	background-color: rgba(0,0,0,0.3) !important;
}

.page .card__text,
.tabs__wrapper {
    max-width: 1200px;
    margin: auto;
}

.tabs__container {
	height: 50px;
}


.page {
    background-size: cover !important;
    background-position: center;
    background-attachment: fixed;
    height: calc(100vh + 50px) !important;
    margin-top: -50px;
    padding: 0;
    padding-top: 50px;
}
#vue .page.card {
	background-color: transparent;
}
.page .card__text{
    margin-top: 50px;
    height: calc(100vh - 100px) !important;
    overflow: auto;
    width: 100vw;
}


.content {
	padding-bottom: 0 !important;
}

/* width */
::-webkit-scrollbar {
    width: 10px;
	background:transparent;
}

/* Track */
::-webkit-scrollbar-track {
    background: transparent;
}

/* Handle */
::-webkit-scrollbar-thumb {
    background: rgba(0,0,0,0.5);
}

/* Handle on hover */
::-webkit-scrollbar-thumb:hover {
    background: rgba(0,0,0,0.7);
}

.icon {
    cursor: pointer;
}
