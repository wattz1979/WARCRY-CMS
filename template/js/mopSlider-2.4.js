/*
* mopSlider 2.4
 * By Hiroki Miura (http://www.mopstudio.jp)
 * Copyright (c) 2009 mopStudio
 * Licensed under the MIT License: http://www.opensource.org/licenses/mit-license.php
 * July 18, 2009
 */

jQuery.fn.extend({
	mopSlider:function(setting){
		var ua=navigator.userAgent,browser,os,ie67=false;
		var px="px"
		var btnPosi,boxPosi,btnPast,boxPast,whitchDrag="";
		var btnMoveNum,btnMoveTo,boxMoveNum,boxMoveTo;
		var timeCheck1,timeCheck2,DragCheck1,DragCheck2,finalTime1,finalTime2;
		var mopSliderName;
		var nextMov=[];
		var scrollMax,scrollNum,caseW,itemNum,sliderNum,checkNum,movNum;
		var mopSliderMotion;
		var boxW,itemMgnAll;
		var finalCount=0;
		var indication=setting.indi
		var mopSliderW=setting.w;
		var mopSliderH=setting.h;
		var sldW=setting.sldW;
		var btnW=setting.btnW;
		var itemMgn=setting.itemMgn;
		var shuffle=setting.shuffle;
		var mopSliderNo=setting.no;
		var mopSliderType=setting.type;
		/*path to image*/
		var btnLeft=new Image();
		var btnLeftF=new Image();
		var btnRight=new Image();
		var btnRightF=new Image();
		var btnCenter=new Image();
		var btnChange=new Image();
		var btnChangeF=new Image();
		var backImg=new Image();
		var sldcLeftImg=new Image();
		var sldcRightImg=new Image();
		var sldBackLeft=new Image();
		var sldBackCenter=new Image();
		var sldBackRight=new Image();
		
		
		if(setting.itemMgn==null){itemMgn=20};
		if(setting.shuffle==null){shuffle=1};
	
		if((mopSliderNo==null)||(mopSliderNo=="01")){
			mopSliderName="#mopSlider01";
			mopSliderNo="01";
		}else{
			mopSliderName="#mopSlider"+setting.no;
		}
		var noSharp=mopSliderName.split("#")[1];
		$(mopSliderName).hide();
		/*shuffle*/
		var arr=jQuery.makeArray($(this).children());
		Array.prototype.shuffle = function() {
		var i = this.length;
		while(i){
			var j = Math.floor(Math.random()*i);
			var t = this[--i];
			this[i] = this[j];
			this[j] = t;
		};
		return this;
		};
		if(shuffle==1){
			arr.shuffle();
			$(arr).appendTo(this);
		};
		if(ua.indexOf("Mac",0)>=0){
			os="mac";
		}else if(ua.indexOf("Win",0)>=0){
			os="win";
		};
		if(ua.indexOf("MSIE 6")>-1){
			browser="ie6";
		};
		if(ua.indexOf("MSIE 7")>-1){
			browser="ie7";
		};
		if((browser=="ie6")||(browser=="ie7")){
			ie67=true;
		};
		$(this).css({position:"absolute",overflow: "hidden",left: "0px",display: "block"});
		/*items number*/
		itemNum=$(this).children().length;
		var allW=0;
		var num=0;
		for (i=1; i<(itemNum+1); i++)
		{
			var itemW=eval($(this).children().eq(num).css("width").split("px")[0]);
			nextMov.push(itemW);
			var itemH=eval($(this).children().eq(num).css("height").split("px")[0]);
			var mgn=(mopSliderH-itemH)/2;
			$(this).children().eq(num).css({marginTop:mgn+px});
			num+=1;
			allW+=itemW - 60;
		};
		
		/*width of all content & margin*/
		itemMgnAll=itemMgn*itemNum;
		boxW=allW+itemMgnAll+itemMgn;
		
		/*put mopSlider*/
		$(this).wrap('<div id="mopSlider" class="slider-holder"><div id="'+noSharp+'"><div class="holder"></div></div></div>');
		$(this).parent().after(
		'<div class="sliderCase">'+
			'<div class="slider">'+
				'<div class="sliderBtn">'+
					'<div class="sldBtnCenter"><div class="indi"></div></div>'+
				'</div>'+
			'</div>'+
		'</div>'
		);
		
		/*set css*/
		$(mopSliderName+" .sliderCase").css({height:"22px",position:"relative",top:"0px"});
		$(mopSliderName+" .sliderBtn").css({position:"absolute",height:"22px",left:"0px",cursor:"pointer"});
		$(mopSliderName+" .sldBtnCenter").css({width:btnW,position:"absolute"});
		
		$(mopSliderName).css({width:mopSliderW+px,height:mopSliderH+28+px});
		$(mopSliderName).css({position:"relative",overflow:"hidden",margin:"0 auto 0 auto"});
		
		if(os=="mac"){$(mopSliderName+" .indi").css({letterSpacing:"0.1em"})};/*foe mac*/
		scrollMax=boxW - mopSliderW;
		scrollNum=scrollMax*0.01;/*0 to100 (box)*/
		sliderNum=(sldW-btnW)*0.01;/*0 to100 (slider)*/
		
		var holderWidth=(mopSliderW+scrollMax*2);
		$(mopSliderName+" .holder").css({width:holderWidth+"px",position:"relative",left:-(scrollMax)+px,cursor:'move'});
		$(mopSliderName+" .holder").children().css({width:boxW+px,left:scrollMax+px});
		$(mopSliderName+" .holder").children().children().css({marginLeft:itemMgn+"px",float:"left",position:"relative"});
		$(mopSliderName+" .sliderCase").css({width:sldW});
		
		var sldCaseW=eval($(mopSliderName+" .sliderCase").css("width").split("px")[0]);
		var sliderLeftMgn=(mopSliderW-sldCaseW)/2;
		
		$(mopSliderName+" .sliderCase").css({});
		$(mopSliderName+" .slider").css({width:sldW+px});
		$(mopSliderName+" .sliderBtn").css({width:btnW+px});
		
		/*pngFix*/
		$(this).pngFix();
		$("#mopSlider .sliderCase,#mopSlider .leftTop,#mopSlider .rightTop").pngFix();
		$("#mopSlider .leftBottom,#mopSlider .rightBottom,#mopSlider .logo").pngFix();
		$(mopSliderName+" .indi").html(indication);
		$(mopSliderName).show();
		
		/*draggable*/
		$(mopSliderName+" .sliderBtn").draggable({
			axis:"x",
			containment:"parent",
			start:function(){
				whitchDrag="btn";
				btnMoveNum=0;
				if(mopSliderNo=="01"){
					DragCheck1=setInterval("mopSliderFunc.DragCheckItv('"+mopSliderNo+"','"+mopSliderName+"','"+whitchDrag+"','"+scrollNum+"','"+boxW+"','"+mopSliderW+"','"+sldW+"','"+btnW+"')",20);
					timeCheck1=setInterval("mopSliderFunc.timeCheckItv('"+mopSliderName+"','"+whitchDrag+"')",50);/*finalMove speed*/
				}
				else if(mopSliderNo=="02"){
					timeCheck2=setInterval("mopSliderFunc.timeCheckItv('"+mopSliderName+"','"+whitchDrag+"')",50);/*finalMove speed*/
					DragCheck2=setInterval("mopSliderFunc.DragCheckItv('"+mopSliderNo+"','"+mopSliderName+"','"+whitchDrag+"','"+scrollNum+"','"+boxW+"','"+mopSliderW+"','"+sldW+"','"+btnW+"')",20);
				}
			},
			drag:function(){},
			stop:function(){
				clearInterval(DragCheck1);
				clearInterval(DragCheck2);
				mopSliderFunc.finalMove(mopSliderName,whitchDrag,sldW,btnW,scrollNum,boxW,mopSliderW);
			}
		});
		$(mopSliderName+" .holder").children().draggable({
			axis:"x",
			containment:"parent",
			start:function(){
				whitchDrag="holder";
				boxMoveNum=0;
				if(mopSliderNo=="01"){
					timeCheck1=setInterval("mopSliderFunc.timeCheckItv()",50);
					DragCheck1=setInterval("mopSliderFunc.DragCheckItv('"+mopSliderNo+"','"+mopSliderName+"','"+whitchDrag+"','"+scrollNum+"','"+boxW+"','"+mopSliderW+"','"+sldW+"','"+btnW+"')",20);
				}
				else if(mopSliderNo=="02"){
					timeCheck2=setInterval("mopSliderFunc.timeCheckItv()",50);
					DragCheck2=setInterval("mopSliderFunc.DragCheckItv('"+mopSliderNo+"','"+mopSliderName+"','"+whitchDrag+"','"+scrollNum+"','"+boxW+"','"+mopSliderW+"','"+sldW+"','"+btnW+"')",20);
				}
				
			},
			drag:function(){},
			stop:function(){
				clearInterval(DragCheck1);
				clearInterval(DragCheck2);
				mopSliderFunc.finalMove(mopSliderName,whitchDrag,sldW,btnW,scrollNum,boxW,mopSliderW);
			}
		});
		$("#mopSlider .sliderBtn").mousedown(
			function(){
				clearInterval(timeCheck1);
				clearInterval(timeCheck2);
				clearInterval(finalTime1);
				clearInterval(finalTime2);
			}
		);
		$("#mopSlider .holder").children().mousedown(
			function(){
				clearInterval(timeCheck1);
				clearInterval(timeCheck2);
				clearInterval(finalTime1);
				clearInterval(finalTime2);
			}
		);
		$(mopSliderName+" .sliderCaseRight").mouseover(
			function(){
				btnPosi=eval($(mopSliderName+" .sliderBtn").css("left").split("px")[0]);
				if(btnPosi!=0){
					$(mopSliderName+" .sliderCaseRight").css({cursor:"pointer"});
					if(browser!="ie6"){
						$(mopSliderName+" .sliderCaseRight").css({backgroundImage:"url("+btnChangeF.src+")"});
					}
				}else{
					$(mopSliderName+" .sliderCaseRight").css({cursor:""});
				}
			}
		);
		$(mopSliderName+" .sliderCaseRight").mouseout(
			function(){
				if(browser!="ie6"){
					$(mopSliderName+" .sliderCaseRight").css({backgroundImage:"url("+btnChange.src+")"});
				}
			}
		);											   
		$(mopSliderName+" .sliderCaseRight").click(
			function(){
				btnPosi=eval($(mopSliderName+" .sliderBtn").css("left").split("px")[0]);
				if(btnPosi!=0){
					if(browser!="ie6"){
						$(mopSliderName+" .sldBtnLeft").css({backgroundImage:"url("+btnLeftF.src+")"});
						$(mopSliderName+" .sldBtnRight").css({backgroundImage:"url("+btnRightF.src+")"});
					}
				}
				$(mopSliderName+" .sliderBtn").animate({left:"0px"},{duration:"1000",easing:"linear",complete:function (){complate()}});
				$(mopSliderName+" .holder").children().animate({left:scrollMax+px},{duration:"1000",easing:"linear",complete:function (){complate()}});
			}
		);
		var complate=function(){
			if(browser!="ie6"){
				$(mopSliderName+" .sldBtnLeft").css({backgroundImage:"url("+btnLeft.src+")"});
				$(mopSliderName+" .sldBtnRight").css({backgroundImage:"url("+btnRight.src+")"});
				$(mopSliderName+" .sliderCaseRight").css({backgroundImage:"url("+btnChange.src+")"});
			}
		}
		mopSliderFunc={
			DragCheckItv:function(mopSliderNo,mopSliderName,whitchDrag,scrollNum,boxW,mopSliderW,sldW,btnW){
				scrollMax=boxW-mopSliderW;
				sliderNum=(sldW-btnW)*0.01;
				var btnPosiPx=$(mopSliderName+" .sliderBtn").css("left");
				var boxPosiPx=$(mopSliderName+" .holder").children().css("left");
				btnPosi=eval(btnPosiPx.split("px")[0]);
				boxPosi=eval(boxPosiPx.split("px")[0]);
				var sliderNum100=btnPosi/sliderNum;
				var boxPosi0=-(boxPosi-scrollMax);
				var boxNum=-(boxPosi0/scrollNum);
				if(whitchDrag=="btn"){
					$(mopSliderName+" .holder").children().css({left:-(sliderNum100*scrollNum)+scrollMax+px});
				}else if(whitchDrag=="holder"){
					$(mopSliderName+" .sliderBtn").css({left:-(boxNum*sliderNum)+px});
				};
			},
			timeCheckItv:function(mopSliderName,whitchDrag){
				btnPast=btnPosi;
				boxPast=boxPosi;
			},
			finalMove:function(mopSliderName,whitchDrag,sldW,btnW,scrollNum,boxW,mopSliderW){
				finalCount=0;
				if((btnPosi!=undefined)&&(btnPast!=undefined))
				{
					btnMoveNumStart=btnPosi-btnPast;
					boxMoveNumStart=boxPosi-boxPast;
					if(mopSliderName=="#mopSlider01"){
						finalTime1=setInterval("mopSliderFunc.finalTimeItv('"+mopSliderName+"','"+whitchDrag+"','"+btnMoveNumStart+"','"+boxMoveNumStart+"','"+sldW+"','"+btnW+"','"+scrollNum+"','"+boxW+"','"+mopSliderW+"')",50);/*last slip move*/
					}
					if(mopSliderName=="#mopSlider02"){
						finalTime2=setInterval("mopSliderFunc.finalTimeItv('"+mopSliderName+"','"+whitchDrag+"','"+btnMoveNumStart+"','"+boxMoveNumStart+"','"+sldW+"','"+btnW+"','"+scrollNum+"','"+boxW+"','"+mopSliderW+"')",50);/*last slip move*/
					}
				}
			},
			finalTimeItv:function(mopSliderName,whitchDrag,btnMoveNumStart,boxMoveNumStart,sldW,btnW,scrollNum,boxW,mopSliderW){
				finalCount+=1;
				if(finalCount==1){
					btnMoveNum=btnMoveNumStart;
					boxMoveNum=boxMoveNumStart;
					mopSliderNameTemp=mopSliderName;
				}
				btnPosi=eval($(mopSliderNameTemp+" .sliderBtn").css("left").split("px")[0]);
				boxPosi=eval($(mopSliderNameTemp+" .holder").children().css("left").split("px")[0]);
				if(whitchDrag=="btn"){
					if((btnMoveNum<0.1)&&(btnMoveNum>-0.1)){
						btnMoveNum=0;
					}else{
						if(browser=="ie6"){
							btnMoveNum=btnMoveNum/1.75;
						}else{
							btnMoveNum=btnMoveNum/1.5;
						}
					}
					btnMoveTo=btnMoveNum+btnPosi;
					if(btnMoveTo>(sldW-btnW)){
						btnMoveTo=sldW-btnW;
					}else if(btnMoveTo<0){
						btnMoveTo=0;
					};
					$(mopSliderNameTemp+" .sliderBtn").css({left:btnMoveTo+px});
					/*set btnPast*/
					btnPast=btnMoveTo;
					
					if(btnMoveNum==0){
						clearInterval(finalTime1);
						clearInterval(finalTime2);
					}
					mopSliderFunc.checkFinal(mopSliderName,whitchDrag,sldW,btnW,scrollNum,boxW,mopSliderW);
				}
				else if(whitchDrag=="holder"){
					if((boxMoveNum<1)&&(boxMoveNum>-1)){
						boxMoveNum=0;
					}else{
						if(browser=="ie6"){
							boxMoveNum=boxMoveNum/1.75;
						}else{
							boxMoveNum=boxMoveNum/1.5;
						}
					}
					boxMoveTo=boxMoveNum+boxPosi;
					if(boxMoveTo>scrollMax){
						boxMoveTo=scrollMax;
					}else if(boxMoveTo<0){
						boxMoveTo=0;
					};
					$(mopSliderNameTemp+" .holder").children().css({left:boxMoveTo+px});
					/*set boxPast*/
					boxPast=boxMoveTo;
					if(boxMoveNum==0){
						clearInterval(finalTime1);
						clearInterval(finalTime2);
					}
					mopSliderFunc.checkFinal(mopSliderName,whitchDrag,sldW,btnW,scrollNum,boxW,mopSliderW);
				}
			},
			checkFinal:function(mopSliderName,whitchDrag,sldW,btnW,scrollNum,boxW,mopSliderW){
				btnPosi=eval($(mopSliderName+" .sliderBtn").css("left").split("px")[0]);
				boxPosi=eval($(mopSliderName+" .holder").children().css("left").split("px")[0]);
				var sliderNum100=btnPosi/sliderNum;
				var boxPosi0=-(boxPosi-scrollMax);
				var boxNum=-(boxPosi0/scrollNum);
				if(whitchDrag=="btn"){
					$(mopSliderName+" .holder").children().css({left:-(sliderNum100*scrollNum)+scrollMax+px});
				}else if(whitchDrag=="holder"){
					$(mopSliderName+" .sliderBtn").css({left:-(boxNum*sliderNum)+px});
				};
				console.log(scrollMax);
			}/*end mopSliderFunc*/
		}
	}
});