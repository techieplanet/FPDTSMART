<html>
    <head>
        <style>
            .header{height: 55px;}
            .header-text{width: 350px;}
            .logo{width: 105px; position: absolute; top: 0; right: 0;}
            
            .row{position: relative; height: 170px;}
            .rowtopmargin {margin-top: 20px;}
            
            .alignleft{text-align: left;}
            .alignright{text-align: right;}
            
            
            .footer{
    background-color:EEEEEE;
    width:90%;
    margin: 0 auto;
}
.footerleft{
   width:90%;
   position:absolute;
    left:0; 
}
 tr:last-child > td { border-bottom: 0; }
.footerright{
    width:10%;
    position:absolute;
    right:0;
}
            .leftfieldset{width: 250px; position: absolute; top: 0; left: 0;}
            .rightfieldset{width: 250px; position: absolute; top:0; right: 0;}
            .rowfieldset{}
            
            
            .fieldsetlegend {
                text-align: center;
                
                font-weight: bold;
            }
            
            .fontsize10 {font-size: 10px;}
            .keybox {width: 20px; height: 15px; display: inline-block;}
            
            .borderall{border:1px solid #ccc;}
            .blackbg{background-color: #000000;}
            .redbg{background-color: #ff0000;}
            .greenbg{background-color: #008000;}
            .bluebg{background-color: #0000ff;}
            .orangebg{background-color: #ffa500;}
            table, td, th {
    border: 1px solid #9ACD32;
}
table {
    border-collapse: collapse;
}


th {
    background-color: #9ACD32;
    color: white;
}
            
        </style>
    </head>
  
    
    <body>
        <div class="container">
            <div class="header">
                
                <div class="header-text alignleft">
                    
                    <strong>Family Planning Dashboard - LGA Report</strong>
                    <br/>
                    <strong>LGA: </strong> %7$s<br/>
					<strong>State: </strong> %6$s<br/>
                    <strong>Month: </strong> %1$s, %2$d
                </div>
                
                <div class="logo alignright">
                    <img  src="pdfrepo/coa.jpg" width="50px" height="53px"  /> 
                </div>
            </div>
            
            <div class="content">
                <hr>
                <br/>
              <div class="row" style="height: 300px;">
                    <fieldset >
                        <legend class="fieldsetlegend">Facility summary, <br/> %5$s</legend>
                        <img src="%8$s" width="520" height="180" />
                    </fieldset>
                  
               
                 
                  
                </div>
              
                
                
                  <div class="row rowtopmargin" style="height: 270px;">
                    <fieldset class="">
                        <legend class="fieldsetlegend">Monthly consumption* in %7$s, %3$s – %4$s</legend>
                        <img src="%9$s" width="505" height="270" />
                        <div class="fontsize10">*Implants and injectables are examples of popular long-acting and short-acting 
                                methods and have been selected here to show general consumption trends of family planning. 
                        </div>
                    </fieldset>
                </div>
               
                firsttablebreak
                
            </div>
                
        </div>
        
    </body>
</html>