body {
	background-image:url('<?php echo BASE_URL;?>/front/img/bg.jpg');
    background-size: cover;
    background-position: center center;
    padding: 0;
    background-attachment: fixed;
}

.page-wrapper, .content .wrap,
.header, .header a, .footer, .footer a {
	background-color: rgba(0,0,0,0.3);
}

.header {
	position: fixed;
	width: 100%;
}

.content {
	padding-top: 50px;
}

h1, h2, h3, h4, h5, h6, .wrap-modules div h2 a, * {
	color: white;
}

.not, .error {
	background-color: rgba(255,125,125,0.5);
}

.done, .ok {
    background-color: rgba(144,238,144,0.5);
}

.wrap {
	padding: 0px 40px;
}

input {
	color: black;
}


.content h1 {
	margin-top: -1.5em;
    position: absolute;
    text-align: center;
    display: block;
    left: 0;
    width: 100%;
    color: white;
    text-shadow: 1px 1px 0px black;
    display: block;
    text-align: center;
    font-size: 5em;
    /* margin: 0.3em auto; */
    font-weight: 100;
    text-transform: capitalize;
}


.content .wrap {
    margin-top: 10em;
}
