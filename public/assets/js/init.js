jQuery(function($){
	var processFile = "assets/inc/ajax.inc.php",

		fx = {
			//创建模态窗口
			"initModal":function(){
				if($(".modal-window").length==0){
					return $("<div>")
						.hide()
						.addClass("modal-window")
						.appendTo("body");
				}else{
					return $(".modal-window");
				}
			},

			//打开模态窗口
			"boxin" : function(data, modal){
				$("<div>")
					.hide()
					.addClass("modal-overlay")
					.click(function(event){
						fx.boxout(event);
					})
					.appendTo("body");
				//数据载入模态窗口
				modal
					.hide()
					.append(data)
					.appendTo("body");
				//淡入模态窗口
				$(".modal-window,.modal-overlay")
					.fadeIn("slow");
			},
			
			//关闭模态窗口
			"boxout" : function(event){
				if(event!=undefined){
					event.preventDefault();
				}
				$("a").removeClass("active");
				$(".modal-window,.modal-overlay")
					.fadeOut("slow",function(){
						$(this).remove();
					})
			},

			//
			"addEvent" : function(data,formData){
				var entry = fx.deserialize(formData),
				//当前月份Date对象
				cal = new Date(NaN),
				//新活动Date对象
				event = new Date(NaN),
				//从H2元素提取月份
				cdata = $("h2").attr("id").split('-'),
				//提取事件起始日期
				date = entry.event_start.split(' ')[0],
				//将活动数据拆分到数组
				edata = date.split('-');
				//设定cal日期对象的值
				cal.setFullYear(cdata[1],cdata[2]-1,1);
				//设定event日期对象的值
				event.setFullYear(edata[0],edata[1]-1,edata[2]);
				//如果日期在当月，把新活动添加
				if(cal.getFullYear()==event.getFullYear() && cal.getMonth()==event.getMonth()){
					var day = String(event.getDate());
					day = day.length==1 ? "0"+day : day;
					data = data==0 ? entry.event_id : data;
					
					//
					$("<a>")
						.hide()
						.attr("href","view.php?event_id="+data)
						.text(entry.event_title)
						.insertAfter($("strong:contains("+day+")"))
						.delay(1000)
						.fadeIn("slow");
				}
			}, 

			//反序列化
			"deserialize" : function(str){
				//拆分每个键值对
				var data = str.split("&"),
				//声明循环要用的变量
				pairs=[],entry={},key,val;
				for( x in data){
					//把键值拆分
					pairs = data[x].split("=");
					key = pairs[0];
					val = pairs[1];
					//键值保存到一个对象
					entry[key] = fx.urldecode(val);
				}
				return entry;
			},

			//清除序列化添加的符号
			"urldecode" : function(str){
				var converted = str.replace(/\+/g,' ');
				return decodeURIComponent(converted);
			},


			//
			"removeEvent" : function(){
				$(".active")
					.remove();
			},

			//
			"editEvent" : function(event){
				event.preventDefault();
				var action = $(event.target).attr("name");
					id = $(event.target)
						.siblings("input[name=event_id]")
						.val();
				id = (id!=undefined) ? "&event_id="+id : "";
				$.ajax({
					type: "POST",
					url: processFile,
					data: "action=" + action + id,
					success: function(data){
						var form = $(data).hide(),
							modal = fx.initModal()
								.children(":not(.modal-close-btn)")
								.remove()
								.end();
						fx.boxin(null,modal);

						form
							.addClass("edit-form")
							.appendTo(modal)
							.fadeIn("slow");
					},
					error: function(msg){
						alert(msg);
					}
				});
			},

			"showPage" : function(page){
				var	modal = fx.initModal()
					.load(""+page+" form");
				fx.boxin(null,modal);
			}
		};

	$.fn.dateZoom.defaults.fontsize = "13px";
	$("li").on("mouseover", "a",function(event){
		$(this).dateZoom();
	});	

	//点击标题将活动信息显示
	$("li").on("click", "a",function(event){
		event.preventDefault();
		$(this).addClass("active");
		var data = $(this)
			.attr("href")
			.replace(/.+?\?(.*)$/,"$1"),
			modal = fx.initModal();

		//关闭模态窗口按钮
		$("<a>")
			.attr("href","#")
			.addClass("modal-close-btn")
			.html("关闭")
			.click(function(event){
				fx.boxout(event);
			})
			.appendTo(modal);

		//载入活动数据		
		$.ajax({
			type:"POST",
			url: processFile,
			data: "action=event_view&" + data,
			success: function(data){
				fx.boxin(data,modal);
			},
			error: function(msg){
				modal.append(msg);
			}
		});
	});


	$(".admin").on("click", function(event){
		fx.editEvent(event);
	});

	$("body").on("click", ".adminButton form", function(event){
		fx.editEvent(event);
	});

	//绑定取消键事件
	$("body").on("click", "a:contains(取消)",function(event){
		fx.boxout(event);
	});

	//绑定提交键事件
	$("body").on("click", ".edit-form input[type=submit]",function(event){
		event.preventDefault();
		
		var formData = $(this).parents("form").serialize(),
			start = $(this).siblings("[name=event_start]").val(),
			end = $(this).siblings("[name=event_end]").val();

		if ($(this).siblings("[name=action]").val()=="event_edit"){
			if(!$.validDate(start)||!$.validDate(end)){
				alert("日期格式错误，请输入 0000-00-00 00:00:00格式日期");
				return;
			}
		}
		$.ajax({
			type: "POST",
			url: processFile,
			data: formData,
			success: function(data){
				fx.removeEvent();
				fx.boxout();
				if($(".edit-form input[name=action]").val()!="confirm_delete"){
					fx.addEvent(data,formData);
				}
			},
			error: function(msg){
				alert(msg);
			}
		});
	});

	$("h2").on("click",function(event){
		event.preventDefault();
		fx.showPage("setDate.php");
	});

	$("a:contains(登陆)").on("click",function(event){
		event.preventDefault();
		fx.showPage("login.php");
	});
});