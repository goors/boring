angular.module('boring',[]);


            function Slider($scope) {

               $scope.items = [
                   {
                       name: 'send',
                       title: '1. Send gift to..'
                   },
                   {
                       name: 'choose',
                       title: '2. Choose perfect gift.. (unicorn, maybe?)'
                       },
                   {
                       name: 'save',
                       title: '3. Something to say?'
                   }

               ];
               
               $scope.selected = $scope.items[0];
               $scope.selection = $scope.items[0];

               $scope.select = function(item) {
                  $scope.selected = item; 
                  $scope.selection = item.name; 
               }
               
               
               if(localStorage.selected_user){
                   $scope.name_selected = localStorage.selected_user;
                   
                   
                   var input = document.createElement("input");
                    input.type = "hidden";
                    input.id = "name"; 
                    input.name = "name"; 
                    input.value = localStorage.selected_user;
                    document.send_gift.appendChild(input); 
                    
                    
                    
                    $scope.selection = 'choose'; 
                    $scope.selected = $scope.items[1]; 
               }
               
               
               $scope.remeber_name = function(name){
                   
                   
                   
                   
                   $scope.selected = $scope.items[1]; 
                   $scope.selection = 'choose'; 
                   $scope.name_selected = name;
                   
                   /*
                    * add hidden filed to parent div
                    */
                   
                    var parent = document.getElementById("send_gift");
                    var child = document.getElementById("name");
                    if(document.contains(child)){
                        parent.removeChild(child);
                    }
                    var input = document.createElement("input");
                    input.type = "hidden";
                    input.id = "name"; 
                    input.name = "name"; 
                    input.value = name;
                    document.send_gift.appendChild(input); 
                    
                    
                    localStorage.selected_user = "";
                    
                    
                   
               }
               $scope.remeber_gift = function(name){
                   $scope.selected = $scope.items[2]; 
                   $scope.selection = 'save'; 
                   $scope.gift_name_selected = name;
                   
                   
                   
                   
                   var parent=document.getElementById("send_gift");
                   var child=document.getElementById("gift");
                   if(document.contains(child)){
                        parent.removeChild(child);
                   }
                    
                   var input = document.createElement("input");
                   input.type = "hidden";
                   input.id = "gift"; 
                   input.name = "gift"; 
                   input.value = name;
                   document.send_gift.appendChild(input);
               }

           }

           function Sendgiftto($scope){
               $scope.save = function(id){
                   localStorage.selected_user = id;
                   window.location.href = "/sendgift"
               }
           }
           function Markasread($scope,  $http){
               $scope.updateModel = function(url, id) {
                $http.get(url);
                document.getElementById("span_"+id).remove();
                
                
                var num = document.getElementById("msg_num").innerText;
                
                var new_num = parseInt(num) - parseInt(1);
                
                document.getElementById("msg_num").innerText = new_num;
              };
           }
           

           