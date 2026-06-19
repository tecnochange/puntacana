<style>

@font-face {
	font-family:Poppins-Light;
	src: url(../../assets/fonts/Poppins-Light.ttf);
}
@font-face {
	font-family:Poppins-Medium;
	src: url(../../assets/fonts/Poppins-Medium.ttf);
}
@font-face {
	font-family:Poppins-ExtraBold;
	src: url(../../assets/fonts/Poppins-ExtraBold.ttf);
}
body{
    background-color: #f5f8fd;
    font-family: Poppins-Light;
    font-size: 14px;
}
h3{
    font-family: Poppins-ExtraBold;
    color: #007BFF;
    margin-bottom: 0;
    font-size: 24px;
}

h5{
    font-family: Poppins-ExtraBold;
}

label{
    margin-top: 6px;
    font-weight: bold;
}

select{
    color: #006dbd !important;
}

/* TABLE */
.table{
    font-size: 13px;
}
.e_web{
	display: inherit;
}
.e_movil{
	display: none;
}
a{
	text-decoration: none;
}

.card{
    border: 1px solid #dddcdc;
    border-radius: 20px;
    box-shadow: 1px 5px 10px rgb(0 0 0 / 10%);
    overflow: hidden;
}
.card-header{
    background-color: #edeff6;
}

.nav-tabs{
    border-bottom: 0px solid #dee2e6;
}

.btn{
    border-radius: 8px;
}

.btn-success{
    background-color: #007bff;
    border: 0px;
}

.btn-primary{
    background-color: #007bff;
    border: 0px;
}
.alert-success{
    background-color: #26b99a;
}
.bg-success{
    background-color: #8BC34A !important;
}
.accordion-button{
    font-size: 13px;
    padding: 3px 20px;
}

.nav-link.active{
    color: #ffffff !important;
    background-color: #007bff !important;
}


/* HEADER */
.header {
    position: fixed;
    width: 100%;
    left: 0px;
    top: 0px;
    background-color: #ffffff;
    display: flex;
    z-index: 100;
    box-shadow: 1px 5px 10px rgb(0 0 0 / 4%);
    border-bottom: 1px solid #d3d3d3;
}
#logo_header {
    width: 280px;
    text-align: center;
}
#navbar_header {
    width: calc(100% - 280px);
}
.btn_menu_lat {
    font-size: 26px;
    color: #212121;
    margin: 14px;
    float: left;
}

/* SIDEBAR */
#sidebar{
	height: 100vh;
	width: 280px;
	margin-left: 0;
	transition: 0.5s all;
	position: fixed;
	top: 0px;
	left: 0px;
	z-index: 2;
}
#sidebar.active {
	left: -280px;
}

.base_lateral {
    background-color: #ffffff;
    border-radius: 20px;
    border: 1px solid #dddcdc;
    height: calc(100% - 80px);
    overflow: hidden;
    margin: 10px;
    box-shadow: 1px 5px 10px rgb(0 0 0 / 10%);
}

.menu_icon{
    font-size: 16px; 
    color: #007bff;
}

.menu_sub_items {
    padding: 6px;
    color: #3c4044;
    padding-left: 30px;
    margin: 6px 10px;
}
.menu_sub_items.active{
	background-color: #007bff;
    border-left: 0;
    border-radius: 8px;
    color: #ffffff;	
}

/* MENU USUARIO */
.menu_usuario {
    text-align: center;
    padding: 15px 20px;
    border-bottom: 1px solid #e5e3e3;
    color: #3f3f3f;
    line-height: 18px;
}

.foto_menu_lateral{
    width: 70px !important;
    height: 70px !important;
    background-size: cover;
    background-position: center;
    border-radius: 100px;
    margin: auto;
}

.foto_miniaturas {
    width: 31px !important;
    height: 31px !important;
    object-fit: cover;
    border-radius: 30px;
    cursor: pointer;
}

.list-unstyled {
    height: calc(100vh - 311px);
    overflow: auto;
}

.menu_groups {
    padding: 6px 8px;
    cursor: pointer;
    color: #000000;
    transition: 0.5s all;
    margin: 6px 10px;
}
.text_lateral {
    /*font-weight: bold;*/
    color: #353535;
}

::-webkit-scrollbar {
  width: 6px;
}
::-webkit-scrollbar-thumb {
  background: #caccd9; 
  border-radius: 5px;
}
::-webkit-scrollbar-thumb:hover {
  background: #e8461f; 
}

/* CONTENT */
#content{
	height: 100vh;
	width: calc( 100% - 280px );
	margin-left: 280px;
	transition: 0.5s all;
	/*overflow: auto;*/
}
	
#content.active {
	width: 100%;
	margin-left: 0;
}

.contanier{
    max-width: 1400px;
}

.solo_movil {
    display: none;
}

.icons_menu_h {
    font-size: 17px;
    padding: 5px 10px;
    cursor: pointer;
    border-radius: 8px;
    border: 0;
    background-color: #007bff;
    color: #ffffff;
}

a[href*="logout"] .icons_menu_h {
    background-color: #dc3545;
}

div.dt-container select.dt-input {
    padding: 4px;
    margin: 0px 10px;
}

@media (max-width: 768px) {
    #sidebar {
        left: -280px;
    }
    #sidebar.active {
        left: 0;
    }
    #content {
        margin-left: 0;
		width: 100%;
    }
    #content.active {
		opacity: 0.2;
    }
    #sidebarCollapse span {
        display: none;
    }
	
	.e_web{
		display: none;
	}
	.e_movil{
		display: inherit;
	}
	.btn_menu_lat{
		float: right;
	}

	#logo_header{
		width: 100%;
	}

	#navbar_header{
		width: auto;
	}

	.solo_movil{
		display: inline;
	}
}
</style>