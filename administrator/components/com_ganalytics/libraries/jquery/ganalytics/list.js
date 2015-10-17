google.load('visualization', '1', {'packages':['Table']});

(function (jQuery){
	var methods = {
		init : function(options) {
			return this.each(function(){
				var settings = {
					chartDivID: 'gaChartDiv',
					chartHeight: '240px',
					gaid: null,
					url: '',
					start: null,
					end: null,
					dateRange: null,
					gaSettings: {},
					pathPrefix: ''
				};
	
				if(options){
					gajQuery.extend(settings, options);
				}
	
				var $this = gajQuery(this);
				var data = $this.data('gaChart');
				if ( ! data ) {
					gajQuery(this).data('gaChart', {
						settings : settings
					});
					var gaChartID = settings.chartDivID;
					$this.append('<div id="'+gaChartID+'_loader" style="text-align: center;"><img src="'+settings.pathPrefix+'media/com_ganalytics/images/ajax-loader.gif" alt="loader" /></div>');
					$this.append('<div id="'+gaChartID+'" class="ganalytics_chart" style="height: '+settings.chartHeight+'"></div>');
				}
			});
		},
		refresh : function(options) {
			return gajQuery(this).each(function(){
				var data = gajQuery(this).data('gaChart');
				if(data == null){
					return;
				}
				var settings = data.settings;

				if(options){
					gajQuery.extend(settings, options);
					gajQuery(this).data('gaChart', {
						settings : settings
					});
				}

				var gaChartID = settings.chartDivID;
				
				gajQuery('#'+gaChartID+'_loader').show();
				
				var url = settings.url;
				if(settings.gaid != null){
					url = url +'&gaid='+settings.gaid;
				}
				if(settings.dateRange != null){
					url = url +'&dateRange='+settings.dateRange;
				}
				if(settings.start != null){
					url = url +'&start-date='+settings.start;
				}
				if(settings.end != null){
					url = url +'&end-date='+settings.end;
				}

				var query = new google.visualization.Query(url);
				query.send(function (response) {
					var chartObject = gajQuery('#'+gaChartID);
			        gajQuery('#'+gaChartID+'_loader').hide();

			        if (response.isError()) {
			    		chartObject.html('<div style="background-color:#E6C0C0;color:#CC0000;padding:10px;font-weight:bold;">'+response.getMessage()+' '+response.getDetailedMessage()+'</div>');
			    		return;
			    	}
			        
			        var table = response.getDataTable();
			        var props = settings.gaSettings;
			        props['width'] = chartObject.width();
			        props['height'] = chartObject.height();
		        	var chart = new google.visualization.Table(chartObject[0]);	        	
		        	props['showRowNumber'] = false;
		        	props['allowHtml'] = true;
		        	props['page'] = 'enable';
			        google.visualization.events.addListener(chart, 'onmouseover', function (e) {
			        	chart.setSelection([e]);
			        });
			        google.visualization.events.addListener(chart, 'onmouseout', function (e) {
			        	chart.setSelection([{'row': null, 'column': null}]);
			        });
			        
			    	chart.draw(table, props);
			    	
			    	gajQuery('#'+gaChartID+' a').fancybox({
	        			width : 700,
	        			height : 500,
	        			autoDimensions : false,
	        			transitionIn : 'elastic',
	        			transitionOut : 'elastic',
	        			speedIn : 600,
	        			speedOut : 200,
	        			type : 'iframe'
	        		});
				});
			});
		},
		show : function( ) {
			return gajQuery(this).each(function(){
	            gajQuery('#'+gajQuery(this).data('gaChart').settings.chartDivID).show();
			});
		},
		hide : function( ) {
			return gajQuery(this).each(function(){
	            gajQuery('#'+gajQuery(this).data('gaChart').settings.chartDivID).hide();
			});
		},
		update : function( content ) { 
		}
	};
	
	gajQuery.fn.gaChart = function(method){
	    if ( methods[method] ) {
	      return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
	    } else if ( typeof method === 'object' || ! method ) {
	      return methods.init.apply( this, arguments );
	    } else {
	      gajQuery.error( 'Method ' +  method + ' does not exist on gajQuery.gaChart' );
	    }    
  }
})(jQuery);