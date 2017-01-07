describe( "RMMaaS Repository models", function () {



      it("MoHol Solution Sample Solution [100, 50, 0, 0]", function () {
		
		  var nodes = [{id: '2', label: 'Inquiry received', type: 'event', group: 'MoHoL Sample Solution', color: '#FF8080', level: 1}
,{id: '17', label: 'Check feasability', type: 'function', group: 'MoHoL Sample Solution', color: '#80ff80', level: 2}
,{id: '9', label: 'xor', type: 'operator', group: 'MoHoL Sample Solution', color: 'gray', level: 3}
,{id: '12', label: 'Inquiry possibly feasible', type: 'event', group: 'MoHoL Sample Solution', color: '#FF8080', level: 4}
,{id: '5', label: 'Bring about clarification', type: 'function', group: 'MoHoL Sample Solution', color: '#80ff80', level: 5}
,{id: '8', label: 'xor', type: 'operator', group: 'MoHoL Sample Solution', color: 'gray', level: 6}
,{id: '1', label: 'Clarification positive', type: 'event', group: 'MoHoL Sample Solution', color: '#FF8080', level: 7}
,{id: '19', label: 'xor', type: 'operator', group: 'MoHoL Sample Solution', color: 'gray', level: 8}
,{id: '14', label: 'Create offer', type: 'function', group: 'MoHoL Sample Solution', color: '#80ff80', level: 9}
,{id: '15', label: 'Offer created', type: 'event', group: 'MoHoL Sample Solution', color: '#FF8080', level: 10}
,{id: '13', label: 'xor', type: 'operator', group: 'MoHoL Sample Solution', color: 'gray', level: 11}
,{id: '3', label: 'Inform customer', type: 'function', group: 'MoHoL Sample Solution', color: '#80ff80', level: 12}
,{id: '4', label: 'Customer informed', type: 'event', group: 'MoHoL Sample Solution', color: '#FF8080', level: 13}
,{id: '16', label: 'Clarification negative', type: 'event', group: 'MoHoL Sample Solution', color: '#FF8080', level: 7}
,{id: '18', label: 'xor', type: 'operator', group: 'MoHoL Sample Solution', color: 'gray', level: 8}
,{id: '7', label: 'Create rejection', type: 'function', group: 'MoHoL Sample Solution', color: '#80ff80', level: 9}
,{id: '10', label: 'Rejection created', type: 'event', group: 'MoHoL Sample Solution', color: '#FF8080', level: 10}
,{id: '11', label: 'Inquiry not feasible', type: 'event', group: 'MoHoL Sample Solution', color: '#FF8080', level: 4}
,{id: '6', label: 'Inquiry feasible', type: 'event', group: 'MoHoL Sample Solution', color: '#FF8080', level: 4}
]; edges = [{from: '1', to: '19', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '2', to: '17', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '3', to: '4', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '5', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '6', to: '19', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '7', to: '10', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '8', to: '1', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '8', to: '16', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '9', to: '12', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '9', to: '11', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '9', to: '6', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '10', to: '13', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '11', to: '18', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '12', to: '5', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '13', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '14', to: '15', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '15', to: '13', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '16', to: '18', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '17', to: '9', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '18', to: '7', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '19', to: '14', arrows:'to', style: 'arrow', color: 'gray'}
]; satelliteObjects = []; relations = [];
		
          var musterErgebnis2 = "";
          expect(setNodesAndEdges(undefined, nodes, edges, satelliteObjects, relations, 100, 50, 0, 0, true)).toEqual(musterErgebnis2);
      });
	
	
	
      it("MoHol Solution Sample Solution [0, 10, 0, 0]", function () {
		
		   var nodes = [{id: '2', label: 'Inquiry received', type: 'event', group: 'MoHoL Sample Solution', color: '#FF8080', level: 1}
,{id: '17', label: 'Check feasability', type: 'function', group: 'MoHoL Sample Solution', color: '#80ff80', level: 2}
,{id: '9', label: 'xor', type: 'operator', group: 'MoHoL Sample Solution', color: 'gray', level: 3}
,{id: '12', label: 'Inquiry possibly feasible', type: 'event', group: 'MoHoL Sample Solution', color: '#FF8080', level: 4}
,{id: '5', label: 'Bring about clarification', type: 'function', group: 'MoHoL Sample Solution', color: '#80ff80', level: 5}
,{id: '8', label: 'xor', type: 'operator', group: 'MoHoL Sample Solution', color: 'gray', level: 6}
,{id: '1', label: 'Clarification positive', type: 'event', group: 'MoHoL Sample Solution', color: '#FF8080', level: 7}
,{id: '19', label: 'xor', type: 'operator', group: 'MoHoL Sample Solution', color: 'gray', level: 8}
,{id: '14', label: 'Create offer', type: 'function', group: 'MoHoL Sample Solution', color: '#80ff80', level: 9}
,{id: '15', label: 'Offer created', type: 'event', group: 'MoHoL Sample Solution', color: '#FF8080', level: 10}
,{id: '13', label: 'xor', type: 'operator', group: 'MoHoL Sample Solution', color: 'gray', level: 11}
,{id: '3', label: 'Inform customer', type: 'function', group: 'MoHoL Sample Solution', color: '#80ff80', level: 12}
,{id: '4', label: 'Customer informed', type: 'event', group: 'MoHoL Sample Solution', color: '#FF8080', level: 13}
,{id: '16', label: 'Clarification negative', type: 'event', group: 'MoHoL Sample Solution', color: '#FF8080', level: 7}
,{id: '18', label: 'xor', type: 'operator', group: 'MoHoL Sample Solution', color: 'gray', level: 8}
,{id: '7', label: 'Create rejection', type: 'function', group: 'MoHoL Sample Solution', color: '#80ff80', level: 9}
,{id: '10', label: 'Rejection created', type: 'event', group: 'MoHoL Sample Solution', color: '#FF8080', level: 10}
,{id: '11', label: 'Inquiry not feasible', type: 'event', group: 'MoHoL Sample Solution', color: '#FF8080', level: 4}
,{id: '6', label: 'Inquiry feasible', type: 'event', group: 'MoHoL Sample Solution', color: '#FF8080', level: 4}
]; edges = [{from: '1', to: '19', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '2', to: '17', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '3', to: '4', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '5', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '6', to: '19', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '7', to: '10', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '8', to: '1', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '8', to: '16', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '9', to: '12', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '9', to: '11', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '9', to: '6', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '10', to: '13', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '11', to: '18', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '12', to: '5', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '13', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '14', to: '15', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '15', to: '13', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '16', to: '18', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '17', to: '9', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '18', to: '7', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '19', to: '14', arrows:'to', style: 'arrow', color: 'gray'}
]; satelliteObjects = []; relations = [];
		
          var musterErgebnis2 = "";
		  
          expect(setNodesAndEdges(undefined, nodes, edges, satelliteObjects, relations, 0, 10, 0, 0, true)).toEqual(musterErgebnis2);
      });
	  
	        it("MoHol Solution 3 [100, 50, 0, 0]", function () {
		
		  var nodes = [{id: '10', label: 'Inquiry received', type: 'event', group: 'MoHoL Solution 03', color: '#FF8080', level: 1}
,{id: '2', label: 'Check feasibility', type: 'function', group: 'MoHoL Solution 03', color: '#80ff80', level: 2}
,{id: '7', label: 'xor', type: 'operator', group: 'MoHoL Solution 03', color: 'gray', level: 3}
,{id: '9', label: 'Inquiry not feasible', type: 'event', group: 'MoHoL Solution 03', color: '#FF8080', level: 4}
,{id: '13', label: 'Create rejection', type: 'function', group: 'MoHoL Solution 03', color: '#80ff80', level: 5}
,{id: '11', label: 'Rejection created', type: 'event', group: 'MoHoL Solution 03', color: '#FF8080', level: 6}
,{id: '4', label: 'xor', type: 'operator', group: 'MoHoL Solution 03', color: 'gray', level: 7}
,{id: '3', label: 'Inform customer', type: 'function', group: 'MoHoL Solution 03', color: '#80ff80', level: 8}
,{id: '15', label: 'Customer informed', type: 'event', group: 'MoHoL Solution 03', color: '#FF8080', level: 9}
,{id: '12', label: 'Inquiry feasible', type: 'event', group: 'MoHoL Solution 03', color: '#FF8080', level: 4}
,{id: '5', label: 'Create offer', type: 'function', group: 'MoHoL Solution 03', color: '#80ff80', level: 5}
,{id: '8', label: 'Offer created', type: 'event', group: 'MoHoL Solution 03', color: '#FF8080', level: 6}
,{id: '6', label: 'Inquiry possibly feasible', type: 'event', group: 'MoHoL Solution 03', color: '#FF8080', level: 4}
,{id: '17', label: 'Bring about clarification', type: 'function', group: 'MoHoL Solution 03', color: '#80ff80', level: 5}
,{id: '1', label: 'xor', type: 'operator', group: 'MoHoL Solution 03', color: 'gray', level: 6}
,{id: '14', label: 'Clarification negative', type: 'event', group: 'MoHoL Solution 03', color: '#FF8080', level: 7}
,{id: '16', label: 'Clarification positive', type: 'event', group: 'MoHoL Solution 03', color: '#FF8080', level: 7}
]; edges = [{from: '1', to: '14', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '1', to: '16', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '2', to: '7', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '3', to: '15', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '4', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '5', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '6', to: '17', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '7', to: '9', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '7', to: '12', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '7', to: '6', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '8', to: '4', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '9', to: '13', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '10', to: '2', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '11', to: '4', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '12', to: '5', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '13', to: '11', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '14', to: '13', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '16', to: '5', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '17', to: '1', arrows:'to', style: 'arrow', color: 'gray'}
]; satelliteObjects = []; relations = [];
		
          var musterErgebnis = "";
		  
          expect(setNodesAndEdges(undefined, nodes, edges, satelliteObjects, relations, 100, 50, 0, 0, true)).toEqual(musterErgebnis);
      });
	
	
	
      it("MoHol Solution 3 [0, 10, 0, 0]", function () {
		
		  var  nodes = [{id: '10', label: 'Inquiry received', type: 'event', group: 'MoHoL Solution 03', color: '#FF8080', level: 1}
,{id: '2', label: 'Check feasibility', type: 'function', group: 'MoHoL Solution 03', color: '#80ff80', level: 2}
,{id: '7', label: 'xor', type: 'operator', group: 'MoHoL Solution 03', color: 'gray', level: 3}
,{id: '9', label: 'Inquiry not feasible', type: 'event', group: 'MoHoL Solution 03', color: '#FF8080', level: 4}
,{id: '13', label: 'Create rejection', type: 'function', group: 'MoHoL Solution 03', color: '#80ff80', level: 5}
,{id: '11', label: 'Rejection created', type: 'event', group: 'MoHoL Solution 03', color: '#FF8080', level: 6}
,{id: '4', label: 'xor', type: 'operator', group: 'MoHoL Solution 03', color: 'gray', level: 7}
,{id: '3', label: 'Inform customer', type: 'function', group: 'MoHoL Solution 03', color: '#80ff80', level: 8}
,{id: '15', label: 'Customer informed', type: 'event', group: 'MoHoL Solution 03', color: '#FF8080', level: 9}
,{id: '12', label: 'Inquiry feasible', type: 'event', group: 'MoHoL Solution 03', color: '#FF8080', level: 4}
,{id: '5', label: 'Create offer', type: 'function', group: 'MoHoL Solution 03', color: '#80ff80', level: 5}
,{id: '8', label: 'Offer created', type: 'event', group: 'MoHoL Solution 03', color: '#FF8080', level: 6}
,{id: '6', label: 'Inquiry possibly feasible', type: 'event', group: 'MoHoL Solution 03', color: '#FF8080', level: 4}
,{id: '17', label: 'Bring about clarification', type: 'function', group: 'MoHoL Solution 03', color: '#80ff80', level: 5}
,{id: '1', label: 'xor', type: 'operator', group: 'MoHoL Solution 03', color: 'gray', level: 6}
,{id: '14', label: 'Clarification negative', type: 'event', group: 'MoHoL Solution 03', color: '#FF8080', level: 7}
,{id: '16', label: 'Clarification positive', type: 'event', group: 'MoHoL Solution 03', color: '#FF8080', level: 7}
]; edges = [{from: '1', to: '14', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '1', to: '16', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '2', to: '7', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '3', to: '15', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '4', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '5', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '6', to: '17', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '7', to: '9', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '7', to: '12', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '7', to: '6', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '8', to: '4', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '9', to: '13', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '10', to: '2', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '11', to: '4', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '12', to: '5', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '13', to: '11', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '14', to: '13', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '16', to: '5', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '17', to: '1', arrows:'to', style: 'arrow', color: 'gray'}
]; satelliteObjects = []; relations = [];
		
          var musterErgebnis = "";
		  
          expect(setNodesAndEdges(undefined, nodes, edges, satelliteObjects, relations, 0, 10, 0, 0, true)).toEqual(musterErgebnis);
      });
	  
	        it("MoHol Solution 4 [100, 50, 0, 0]", function () {
		
		  var nodes = [{id: '3', label: 'Inquiry received', type: 'event', group: 'MoHoL Solution 04', color: '#FF8080', level: 1}
,{id: '9', label: 'Check feasibility', type: 'function', group: 'MoHoL Solution 04', color: '#80ff80', level: 2}
,{id: '7', label: 'xor', type: 'operator', group: 'MoHoL Solution 04', color: 'gray', level: 3}
,{id: '6', label: 'Inquiry possibly feasible', type: 'event', group: 'MoHoL Solution 04', color: '#FF8080', level: 4}
,{id: '10', label: 'Bring about clarification', type: 'function', group: 'MoHoL Solution 04', color: '#80ff80', level: 5}
,{id: '19', label: 'xor', type: 'operator', group: 'MoHoL Solution 04', color: 'gray', level: 6}
,{id: '4', label: 'Clarification negative', type: 'event', group: 'MoHoL Solution 04', color: '#FF8080', level: 7}
,{id: '17', label: 'or', type: 'operator', group: 'MoHoL Solution 04', color: 'gray', level: 8}
,{id: '2', label: 'Create rejection', type: 'function', group: 'MoHoL Solution 04', color: '#80ff80', level: 9}
,{id: '18', label: 'Rejection created', type: 'event', group: 'MoHoL Solution 04', color: '#FF8080', level: 10}
,{id: '5', label: 'xor', type: 'operator', group: 'MoHoL Solution 04', color: 'gray', level: 11}
,{id: '8', label: 'Inform customer', type: 'function', group: 'MoHoL Solution 04', color: '#80ff80', level: 12}
,{id: '14', label: 'Customer informed', type: 'event', group: 'MoHoL Solution 04', color: '#FF8080', level: 13}
,{id: '13', label: 'Clarification positive', type: 'event', group: 'MoHoL Solution 04', color: '#FF8080', level: 7}
,{id: '12', label: 'or', type: 'operator', group: 'MoHoL Solution 04', color: 'gray', level: 8}
,{id: '11', label: 'Create offer', type: 'function', group: 'MoHoL Solution 04', color: '#80ff80', level: 9}
,{id: '15', label: 'Offer created', type: 'event', group: 'MoHoL Solution 04', color: '#FF8080', level: 10}
,{id: '16', label: 'Inquiry feasible', type: 'event', group: 'MoHoL Solution 04', color: '#FF8080', level: 4}
,{id: '1', label: 'Inquiry not feasible', type: 'event', group: 'MoHoL Solution 04', color: '#FF8080', level: 4}
]; edges = [{from: '1', to: '17', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '2', to: '18', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '3', to: '9', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '4', to: '17', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '5', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '6', to: '10', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '7', to: '6', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '7', to: '16', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '7', to: '1', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '8', to: '14', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '9', to: '7', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '10', to: '19', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '11', to: '15', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '12', to: '11', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '13', to: '12', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '15', to: '5', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '16', to: '12', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '17', to: '2', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '18', to: '5', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '19', to: '4', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '19', to: '13', arrows:'to', style: 'arrow', color: 'gray'}
]; satelliteObjects = []; relations = [];
		
          var musterErgebnis = "";

          expect(setNodesAndEdges(undefined, nodes, edges, satelliteObjects, relations, 100, 50, 0, 0, true)).toEqual(musterErgebnis);
      });
	
	
	
      it("MoHol Solution 4 [0, 10, 0, 0]", function () {
		
		  var  nodes = [{id: '3', label: 'Inquiry received', type: 'event', group: 'MoHoL Solution 04', color: '#FF8080', level: 1}
,{id: '9', label: 'Check feasibility', type: 'function', group: 'MoHoL Solution 04', color: '#80ff80', level: 2}
,{id: '7', label: 'xor', type: 'operator', group: 'MoHoL Solution 04', color: 'gray', level: 3}
,{id: '6', label: 'Inquiry possibly feasible', type: 'event', group: 'MoHoL Solution 04', color: '#FF8080', level: 4}
,{id: '10', label: 'Bring about clarification', type: 'function', group: 'MoHoL Solution 04', color: '#80ff80', level: 5}
,{id: '19', label: 'xor', type: 'operator', group: 'MoHoL Solution 04', color: 'gray', level: 6}
,{id: '4', label: 'Clarification negative', type: 'event', group: 'MoHoL Solution 04', color: '#FF8080', level: 7}
,{id: '17', label: 'or', type: 'operator', group: 'MoHoL Solution 04', color: 'gray', level: 8}
,{id: '2', label: 'Create rejection', type: 'function', group: 'MoHoL Solution 04', color: '#80ff80', level: 9}
,{id: '18', label: 'Rejection created', type: 'event', group: 'MoHoL Solution 04', color: '#FF8080', level: 10}
,{id: '5', label: 'xor', type: 'operator', group: 'MoHoL Solution 04', color: 'gray', level: 11}
,{id: '8', label: 'Inform customer', type: 'function', group: 'MoHoL Solution 04', color: '#80ff80', level: 12}
,{id: '14', label: 'Customer informed', type: 'event', group: 'MoHoL Solution 04', color: '#FF8080', level: 13}
,{id: '13', label: 'Clarification positive', type: 'event', group: 'MoHoL Solution 04', color: '#FF8080', level: 7}
,{id: '12', label: 'or', type: 'operator', group: 'MoHoL Solution 04', color: 'gray', level: 8}
,{id: '11', label: 'Create offer', type: 'function', group: 'MoHoL Solution 04', color: '#80ff80', level: 9}
,{id: '15', label: 'Offer created', type: 'event', group: 'MoHoL Solution 04', color: '#FF8080', level: 10}
,{id: '16', label: 'Inquiry feasible', type: 'event', group: 'MoHoL Solution 04', color: '#FF8080', level: 4}
,{id: '1', label: 'Inquiry not feasible', type: 'event', group: 'MoHoL Solution 04', color: '#FF8080', level: 4}
]; edges = [{from: '1', to: '17', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '2', to: '18', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '3', to: '9', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '4', to: '17', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '5', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '6', to: '10', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '7', to: '6', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '7', to: '16', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '7', to: '1', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '8', to: '14', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '9', to: '7', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '10', to: '19', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '11', to: '15', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '12', to: '11', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '13', to: '12', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '15', to: '5', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '16', to: '12', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '17', to: '2', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '18', to: '5', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '19', to: '4', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '19', to: '13', arrows:'to', style: 'arrow', color: 'gray'}
]; satelliteObjects = []; relations = [];
		
          var musterErgebnis = "";

          expect(setNodesAndEdges(undefined, nodes, edges, satelliteObjects, relations, 0, 10, 0, 0, true)).toEqual(musterErgebnis);
      });
	  
	        it("MoHol Solution 5 [100, 50, 0, 0]", function () {
		
		  var nodes = [{id: '13', label: 'Inquiry receivt', type: 'event', group: 'MoHoL Solution 05', color: '#FF8080', level: 1}
,{id: '18', label: 'Check to feasibility', type: 'function', group: 'MoHoL Solution 05', color: '#80ff80', level: 2}
,{id: '8', label: 'Feasibility checked', type: 'event', group: 'MoHoL Solution 05', color: '#FF8080', level: 3}
,{id: '9', label: 'Result leading', type: 'function', group: 'MoHoL Solution 05', color: '#80ff80', level: 4}
,{id: '6', label: 'xor', type: 'operator', group: 'MoHoL Solution 05', color: 'gray', level: 5}
,{id: '1', label: 'feasible', type: 'event', group: 'MoHoL Solution 05', color: '#FF8080', level: 6}
,{id: '16', label: 'possibly feasible', type: 'event', group: 'MoHoL Solution 05', color: '#FF8080', level: 6}
,{id: '3', label: 'Bring about clarification', type: 'function', group: 'MoHoL Solution 05', color: '#80ff80', level: 7}
,{id: '4', label: 'Clarification brought about', type: 'event', group: 'MoHoL Solution 05', color: '#FF8080', level: 8}
,{id: '15', label: 'Result giving', type: 'function', group: 'MoHoL Solution 05', color: '#80ff80', level: 9}
,{id: '12', label: 'xor', type: 'operator', group: 'MoHoL Solution 05', color: 'gray', level: 10}
,{id: '2', label: 'negative', type: 'event', group: 'MoHoL Solution 05', color: '#FF8080', level: 11}
,{id: '5', label: 'Create rejection', type: 'function', group: 'MoHoL Solution 05', color: '#80ff80', level: 12}
,{id: '7', label: 'Rejection created', type: 'event', group: 'MoHoL Solution 05', color: '#FF8080', level: 13}
,{id: '17', label: 'positive', type: 'event', group: 'MoHoL Solution 05', color: '#FF8080', level: 11}
,{id: '14', label: 'not feasible', type: 'event', group: 'MoHoL Solution 05', color: '#FF8080', level: 6}
,{id: '11', label: 'and', type: 'operator', group: 'MoHoL Solution 05', color: 'gray', level: 7}
,{id: '10', label: 'Inform customer', type: 'function', group: 'MoHoL Solution 05', color: '#80ff80', level: 8}
]; edges = [{from: '2', to: '5', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '3', to: '4', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '4', to: '15', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '5', to: '7', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '6', to: '1', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '6', to: '16', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '6', to: '14', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '8', to: '9', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '9', to: '6', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '11', to: '5', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '11', to: '10', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '12', to: '2', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '12', to: '17', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '13', to: '18', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '14', to: '11', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '15', to: '12', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '16', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '18', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
]; satelliteObjects = []; relations = [];
		
          var musterErgebnis = "";

          expect(setNodesAndEdges(undefined, nodes, edges, satelliteObjects, relations, 100, 50, 0, 0, true)).toEqual(musterErgebnis);
      });
	
	
	
      it("MoHol Solution 5 [0, 10, 0, 0]", function () {
		
		  var nodes = [{id: '13', label: 'Inquiry receivt', type: 'event', group: 'MoHoL Solution 05', color: '#FF8080', level: 1}
,{id: '18', label: 'Check to feasibility', type: 'function', group: 'MoHoL Solution 05', color: '#80ff80', level: 2}
,{id: '8', label: 'Feasibility checked', type: 'event', group: 'MoHoL Solution 05', color: '#FF8080', level: 3}
,{id: '9', label: 'Result leading', type: 'function', group: 'MoHoL Solution 05', color: '#80ff80', level: 4}
,{id: '6', label: 'xor', type: 'operator', group: 'MoHoL Solution 05', color: 'gray', level: 5}
,{id: '1', label: 'feasible', type: 'event', group: 'MoHoL Solution 05', color: '#FF8080', level: 6}
,{id: '16', label: 'possibly feasible', type: 'event', group: 'MoHoL Solution 05', color: '#FF8080', level: 6}
,{id: '3', label: 'Bring about clarification', type: 'function', group: 'MoHoL Solution 05', color: '#80ff80', level: 7}
,{id: '4', label: 'Clarification brought about', type: 'event', group: 'MoHoL Solution 05', color: '#FF8080', level: 8}
,{id: '15', label: 'Result giving', type: 'function', group: 'MoHoL Solution 05', color: '#80ff80', level: 9}
,{id: '12', label: 'xor', type: 'operator', group: 'MoHoL Solution 05', color: 'gray', level: 10}
,{id: '2', label: 'negative', type: 'event', group: 'MoHoL Solution 05', color: '#FF8080', level: 11}
,{id: '5', label: 'Create rejection', type: 'function', group: 'MoHoL Solution 05', color: '#80ff80', level: 12}
,{id: '7', label: 'Rejection created', type: 'event', group: 'MoHoL Solution 05', color: '#FF8080', level: 13}
,{id: '17', label: 'positive', type: 'event', group: 'MoHoL Solution 05', color: '#FF8080', level: 11}
,{id: '14', label: 'not feasible', type: 'event', group: 'MoHoL Solution 05', color: '#FF8080', level: 6}
,{id: '11', label: 'and', type: 'operator', group: 'MoHoL Solution 05', color: 'gray', level: 7}
,{id: '10', label: 'Inform customer', type: 'function', group: 'MoHoL Solution 05', color: '#80ff80', level: 8}
]; edges = [{from: '2', to: '5', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '3', to: '4', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '4', to: '15', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '5', to: '7', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '6', to: '1', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '6', to: '16', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '6', to: '14', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '8', to: '9', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '9', to: '6', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '11', to: '5', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '11', to: '10', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '12', to: '2', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '12', to: '17', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '13', to: '18', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '14', to: '11', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '15', to: '12', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '16', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '18', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
]; satelliteObjects = []; relations = [];
		
          var musterErgebnis = "";

          expect(setNodesAndEdges(undefined, nodes, edges, satelliteObjects, relations, 0, 10, 0, 0, true)).toEqual(musterErgebnis);
      });
	  
	        it("MoHol Solution 6 [100, 50, 0, 0]", function () {
		
		  var nodes = [{id: '4', label: 'Inquiry is present', type: 'event', group: 'MoHoL Solution 06', color: '#FF8080', level: 1}
,{id: '11', label: 'Check feasibility', type: 'function', group: 'MoHoL Solution 06', color: '#80ff80', level: 2}
,{id: '14', label: 'xor', type: 'operator', group: 'MoHoL Solution 06', color: 'gray', level: 3}
,{id: '7', label: 'I. not feasible', type: 'event', group: 'MoHoL Solution 06', color: '#FF8080', level: 4}
,{id: '15', label: 'or', type: 'operator', group: 'MoHoL Solution 06', color: 'gray', level: 5}
,{id: '10', label: 'Create rejection + inform customer', type: 'function', group: 'MoHoL Solution 06', color: '#80ff80', level: 6}
,{id: '2', label: 'Customer rejected', type: 'event', group: 'MoHoL Solution 06', color: '#FF8080', level: 7}
,{id: '1', label: 'I. feasible', type: 'event', group: 'MoHoL Solution 06', color: '#FF8080', level: 4}
,{id: '13', label: 'or', type: 'operator', group: 'MoHoL Solution 06', color: 'gray', level: 5}
,{id: '12', label: 'Create offer + inform c.', type: 'function', group: 'MoHoL Solution 06', color: '#80ff80', level: 6}
,{id: '16', label: 'Customer accepted + informed', type: 'event', group: 'MoHoL Solution 06', color: '#FF8080', level: 7}
,{id: '6', label: 'I. possibly Feasible', type: 'event', group: 'MoHoL Solution 06', color: '#FF8080', level: 4}
,{id: '5', label: 'Bring about clarification', type: 'function', group: 'MoHoL Solution 06', color: '#80ff80', level: 5}
,{id: '8', label: 'xor', type: 'operator', group: 'MoHoL Solution 06', color: 'gray', level: 6}
,{id: '3', label: 'R. positive', type: 'event', group: 'MoHoL Solution 06', color: '#FF8080', level: 7}
,{id: '9', label: 'R. negative', type: 'event', group: 'MoHoL Solution 06', color: '#FF8080', level: 7}
]; edges = [{from: '1', to: '13', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '3', to: '13', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '4', to: '11', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '5', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '6', to: '5', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '7', to: '15', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '8', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '8', to: '9', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '9', to: '15', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '10', to: '2', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '11', to: '14', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '12', to: '16', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '13', to: '12', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '14', to: '7', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '14', to: '1', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '14', to: '6', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '15', to: '10', arrows:'to', style: 'arrow', color: 'gray'}
]; satelliteObjects = []; relations = [];
		
          var musterErgebnis = "";

          expect(setNodesAndEdges(undefined, nodes, edges, satelliteObjects, relations, 100, 50, 0, 0, true)).toEqual(musterErgebnis);
      });
	
	
	
      it("MoHol Solution 6 [0, 10, 0, 0]", function () {
		
		  var  nodes = [{id: '4', label: 'Inquiry is present', type: 'event', group: 'MoHoL Solution 06', color: '#FF8080', level: 1}
,{id: '11', label: 'Check feasibility', type: 'function', group: 'MoHoL Solution 06', color: '#80ff80', level: 2}
,{id: '14', label: 'xor', type: 'operator', group: 'MoHoL Solution 06', color: 'gray', level: 3}
,{id: '7', label: 'I. not feasible', type: 'event', group: 'MoHoL Solution 06', color: '#FF8080', level: 4}
,{id: '15', label: 'or', type: 'operator', group: 'MoHoL Solution 06', color: 'gray', level: 5}
,{id: '10', label: 'Create rejection + inform customer', type: 'function', group: 'MoHoL Solution 06', color: '#80ff80', level: 6}
,{id: '2', label: 'Customer rejected', type: 'event', group: 'MoHoL Solution 06', color: '#FF8080', level: 7}
,{id: '1', label: 'I. feasible', type: 'event', group: 'MoHoL Solution 06', color: '#FF8080', level: 4}
,{id: '13', label: 'or', type: 'operator', group: 'MoHoL Solution 06', color: 'gray', level: 5}
,{id: '12', label: 'Create offer + inform c.', type: 'function', group: 'MoHoL Solution 06', color: '#80ff80', level: 6}
,{id: '16', label: 'Customer accepted + informed', type: 'event', group: 'MoHoL Solution 06', color: '#FF8080', level: 7}
,{id: '6', label: 'I. possibly Feasible', type: 'event', group: 'MoHoL Solution 06', color: '#FF8080', level: 4}
,{id: '5', label: 'Bring about clarification', type: 'function', group: 'MoHoL Solution 06', color: '#80ff80', level: 5}
,{id: '8', label: 'xor', type: 'operator', group: 'MoHoL Solution 06', color: 'gray', level: 6}
,{id: '3', label: 'R. positive', type: 'event', group: 'MoHoL Solution 06', color: '#FF8080', level: 7}
,{id: '9', label: 'R. negative', type: 'event', group: 'MoHoL Solution 06', color: '#FF8080', level: 7}
]; edges = [{from: '1', to: '13', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '3', to: '13', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '4', to: '11', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '5', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '6', to: '5', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '7', to: '15', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '8', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '8', to: '9', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '9', to: '15', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '10', to: '2', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '11', to: '14', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '12', to: '16', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '13', to: '12', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '14', to: '7', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '14', to: '1', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '14', to: '6', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '15', to: '10', arrows:'to', style: 'arrow', color: 'gray'}
]; satelliteObjects = []; relations = [];
		
          var musterErgebnis = "";

          expect(setNodesAndEdges(undefined, nodes, edges, satelliteObjects, relations, 0, 10, 0, 0, true)).toEqual(musterErgebnis);
      });
	  
	        it("MoHol Solution 8 [100, 50, 0, 0]", function () {
		
		  var nodes = [{id: '16', label: 'Customer inquired', type: 'event', group: 'MoHoL Solution 08', color: '#FF8080', level: 1}
,{id: '17', label: 'Record inquiry in system', type: 'function', group: 'MoHoL Solution 08', color: '#80ff80', level: 2}
,{id: '11', label: 'Inquiry received', type: 'event', group: 'MoHoL Solution 08', color: '#FF8080', level: 3}
,{id: '14', label: 'Check feasibility', type: 'function', group: 'MoHoL Solution 08', color: '#80ff80', level: 4}
,{id: '10', label: 'xor', type: 'operator', group: 'MoHoL Solution 08', color: 'gray', level: 5}
,{id: '8', label: 'Inquiry feasible', type: 'event', group: 'MoHoL Solution 08', color: '#FF8080', level: 6}
,{id: '18', label: 'xor', type: 'operator', group: 'MoHoL Solution 08', color: 'gray', level: 7}
,{id: '6', label: 'Create offer', type: 'function', group: 'MoHoL Solution 08', color: '#80ff80', level: 8}
,{id: '9', label: 'xor', type: 'operator', group: 'MoHoL Solution 08', color: 'gray', level: 9}
,{id: '4', label: 'Customer informed', type: 'event', group: 'MoHoL Solution 08', color: '#FF8080', level: 10}
,{id: '5', label: 'Inquiry possibly feasible', type: 'event', group: 'MoHoL Solution 08', color: '#FF8080', level: 6}
,{id: '1', label: 'Bring about clarification', type: 'function', group: 'MoHoL Solution 08', color: '#80ff80', level: 7}
,{id: '7', label: 'and', type: 'operator', group: 'MoHoL Solution 08', color: 'gray', level: 8}
,{id: '12', label: 'Clarification negative', type: 'event', group: 'MoHoL Solution 08', color: '#FF8080', level: 9}
,{id: '2', label: 'xor', type: 'operator', group: 'MoHoL Solution 08', color: 'gray', level: 10}
,{id: '13', label: 'Create rejection', type: 'function', group: 'MoHoL Solution 08', color: '#80ff80', level: 11}
,{id: '3', label: 'Clarification positive', type: 'event', group: 'MoHoL Solution 08', color: '#FF8080', level: 9}
,{id: '15', label: 'Inquiry not feasible', type: 'event', group: 'MoHoL Solution 08', color: '#FF8080', level: 6}
]; edges = [{from: '1', to: '7', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '2', to: '13', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '3', to: '18', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '5', to: '1', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '6', to: '9', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '7', to: '12', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '7', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '8', to: '18', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '9', to: '4', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '10', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '10', to: '5', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '10', to: '15', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '11', to: '14', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '12', to: '2', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '13', to: '9', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '14', to: '10', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '15', to: '2', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '16', to: '17', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '17', to: '11', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '18', to: '6', arrows:'to', style: 'arrow', color: 'gray'}
]; satelliteObjects = []; relations = [];
		
          var musterErgebnis = "";

          expect(setNodesAndEdges(undefined, nodes, edges, satelliteObjects, relations, 100, 50, 0, 0, true)).toEqual(musterErgebnis);
      });
	
	
	
      it("MoHol Solution 8 [0, 10, 0, 0]", function () {
		
		  var  nodes = [{id: '16', label: 'Customer inquired', type: 'event', group: 'MoHoL Solution 08', color: '#FF8080', level: 1}
,{id: '17', label: 'Record inquiry in system', type: 'function', group: 'MoHoL Solution 08', color: '#80ff80', level: 2}
,{id: '11', label: 'Inquiry received', type: 'event', group: 'MoHoL Solution 08', color: '#FF8080', level: 3}
,{id: '14', label: 'Check feasibility', type: 'function', group: 'MoHoL Solution 08', color: '#80ff80', level: 4}
,{id: '10', label: 'xor', type: 'operator', group: 'MoHoL Solution 08', color: 'gray', level: 5}
,{id: '8', label: 'Inquiry feasible', type: 'event', group: 'MoHoL Solution 08', color: '#FF8080', level: 6}
,{id: '18', label: 'xor', type: 'operator', group: 'MoHoL Solution 08', color: 'gray', level: 7}
,{id: '6', label: 'Create offer', type: 'function', group: 'MoHoL Solution 08', color: '#80ff80', level: 8}
,{id: '9', label: 'xor', type: 'operator', group: 'MoHoL Solution 08', color: 'gray', level: 9}
,{id: '4', label: 'Customer informed', type: 'event', group: 'MoHoL Solution 08', color: '#FF8080', level: 10}
,{id: '5', label: 'Inquiry possibly feasible', type: 'event', group: 'MoHoL Solution 08', color: '#FF8080', level: 6}
,{id: '1', label: 'Bring about clarification', type: 'function', group: 'MoHoL Solution 08', color: '#80ff80', level: 7}
,{id: '7', label: 'and', type: 'operator', group: 'MoHoL Solution 08', color: 'gray', level: 8}
,{id: '12', label: 'Clarification negative', type: 'event', group: 'MoHoL Solution 08', color: '#FF8080', level: 9}
,{id: '2', label: 'xor', type: 'operator', group: 'MoHoL Solution 08', color: 'gray', level: 10}
,{id: '13', label: 'Create rejection', type: 'function', group: 'MoHoL Solution 08', color: '#80ff80', level: 11}
,{id: '3', label: 'Clarification positive', type: 'event', group: 'MoHoL Solution 08', color: '#FF8080', level: 9}
,{id: '15', label: 'Inquiry not feasible', type: 'event', group: 'MoHoL Solution 08', color: '#FF8080', level: 6}
]; edges = [{from: '1', to: '7', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '2', to: '13', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '3', to: '18', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '5', to: '1', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '6', to: '9', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '7', to: '12', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '7', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '8', to: '18', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '9', to: '4', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '10', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '10', to: '5', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '10', to: '15', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '11', to: '14', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '12', to: '2', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '13', to: '9', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '14', to: '10', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '15', to: '2', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '16', to: '17', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '17', to: '11', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '18', to: '6', arrows:'to', style: 'arrow', color: 'gray'}
]; satelliteObjects = []; relations = [];
		
          var musterErgebnis = "";

          expect(setNodesAndEdges(undefined, nodes, edges, satelliteObjects, relations, 0, 10, 0, 0, true)).toEqual(musterErgebnis);
      });
	  
	        it("MoHol Solution 9 [100, 50, 0, 0]", function () {
		
		  var nodes = [{id: '14', label: 'Received inquiry', type: 'event', group: 'MoHoL Solution 09', color: '#FF8080', level: 1}
,{id: '9', label: 'Feasibility check', type: 'function', group: 'MoHoL Solution 09', color: '#80ff80', level: 2}
,{id: '17', label: 'xor', type: 'operator', group: 'MoHoL Solution 09', color: 'gray', level: 3}
,{id: '3', label: 'I. poss. feasible', type: 'event', group: 'MoHoL Solution 09', color: '#FF8080', level: 4}
,{id: '5', label: 'Bring about a clarification', type: 'function', group: 'MoHoL Solution 09', color: '#80ff80', level: 5}
,{id: '1', label: 'xor', type: 'operator', group: 'MoHoL Solution 09', color: 'gray', level: 6}
,{id: '7', label: 'negative', type: 'event', group: 'MoHoL Solution 09', color: '#FF8080', level: 7}
,{id: '2', label: 'xor', type: 'operator', group: 'MoHoL Solution 09', color: 'gray', level: 8}
,{id: '12', label: 'Reject', type: 'function', group: 'MoHoL Solution 09', color: '#80ff80', level: 9}
,{id: '11', label: 'Rejection created', type: 'event', group: 'MoHoL Solution 09', color: '#FF8080', level: 10}
,{id: '4', label: 'xor', type: 'operator', group: 'MoHoL Solution 09', color: 'gray', level: 11}
,{id: '6', label: 'Inform customer', type: 'function', group: 'MoHoL Solution 09', color: '#80ff80', level: 12}
,{id: '13', label: 'Customer information', type: 'event', group: 'MoHoL Solution 09', color: '#FF8080', level: 13}
,{id: '10', label: 'pos.', type: 'event', group: 'MoHoL Solution 09', color: '#FF8080', level: 7}
,{id: '19', label: 'xor', type: 'operator', group: 'MoHoL Solution 09', color: 'gray', level: 8}
,{id: '15', label: 'Creation of offer', type: 'function', group: 'MoHoL Solution 09', color: '#80ff80', level: 9}
,{id: '8', label: 'Created offer', type: 'event', group: 'MoHoL Solution 09', color: '#FF8080', level: 10}
,{id: '16', label: 'Inquiry feasible', type: 'event', group: 'MoHoL Solution 09', color: '#FF8080', level: 4}
,{id: '18', label: 'I. not feasible', type: 'event', group: 'MoHoL Solution 09', color: '#FF8080', level: 4}
]; edges = [{from: '1', to: '7', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '1', to: '10', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '2', to: '12', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '3', to: '5', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '4', to: '6', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '5', to: '1', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '6', to: '13', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '7', to: '2', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '8', to: '4', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '9', to: '17', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '10', to: '19', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '11', to: '4', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '12', to: '11', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '14', to: '9', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '15', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '16', to: '19', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '17', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '17', to: '16', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '17', to: '18', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '18', to: '2', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '19', to: '15', arrows:'to', style: 'arrow', color: 'gray'}
]; satelliteObjects = []; relations = [];
		
          var musterErgebnis = "";

          expect(setNodesAndEdges(undefined, nodes, edges, satelliteObjects, relations, 100, 50, 0, 0, true)).toEqual(musterErgebnis);
      });
	
	
	
      it("MoHol Solution 9 [0, 10, 0, 0]", function () {
		
		  var  nodes = [{id: '14', label: 'Received inquiry', type: 'event', group: 'MoHoL Solution 09', color: '#FF8080', level: 1}
,{id: '9', label: 'Feasibility check', type: 'function', group: 'MoHoL Solution 09', color: '#80ff80', level: 2}
,{id: '17', label: 'xor', type: 'operator', group: 'MoHoL Solution 09', color: 'gray', level: 3}
,{id: '3', label: 'I. poss. feasible', type: 'event', group: 'MoHoL Solution 09', color: '#FF8080', level: 4}
,{id: '5', label: 'Bring about a clarification', type: 'function', group: 'MoHoL Solution 09', color: '#80ff80', level: 5}
,{id: '1', label: 'xor', type: 'operator', group: 'MoHoL Solution 09', color: 'gray', level: 6}
,{id: '7', label: 'negative', type: 'event', group: 'MoHoL Solution 09', color: '#FF8080', level: 7}
,{id: '2', label: 'xor', type: 'operator', group: 'MoHoL Solution 09', color: 'gray', level: 8}
,{id: '12', label: 'Reject', type: 'function', group: 'MoHoL Solution 09', color: '#80ff80', level: 9}
,{id: '11', label: 'Rejection created', type: 'event', group: 'MoHoL Solution 09', color: '#FF8080', level: 10}
,{id: '4', label: 'xor', type: 'operator', group: 'MoHoL Solution 09', color: 'gray', level: 11}
,{id: '6', label: 'Inform customer', type: 'function', group: 'MoHoL Solution 09', color: '#80ff80', level: 12}
,{id: '13', label: 'Customer information', type: 'event', group: 'MoHoL Solution 09', color: '#FF8080', level: 13}
,{id: '10', label: 'pos.', type: 'event', group: 'MoHoL Solution 09', color: '#FF8080', level: 7}
,{id: '19', label: 'xor', type: 'operator', group: 'MoHoL Solution 09', color: 'gray', level: 8}
,{id: '15', label: 'Creation of offer', type: 'function', group: 'MoHoL Solution 09', color: '#80ff80', level: 9}
,{id: '8', label: 'Created offer', type: 'event', group: 'MoHoL Solution 09', color: '#FF8080', level: 10}
,{id: '16', label: 'Inquiry feasible', type: 'event', group: 'MoHoL Solution 09', color: '#FF8080', level: 4}
,{id: '18', label: 'I. not feasible', type: 'event', group: 'MoHoL Solution 09', color: '#FF8080', level: 4}
]; edges = [{from: '1', to: '7', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '1', to: '10', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '2', to: '12', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '3', to: '5', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '4', to: '6', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '5', to: '1', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '6', to: '13', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '7', to: '2', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '8', to: '4', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '9', to: '17', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '10', to: '19', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '11', to: '4', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '12', to: '11', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '14', to: '9', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '15', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '16', to: '19', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '17', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '17', to: '16', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '17', to: '18', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '18', to: '2', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '19', to: '15', arrows:'to', style: 'arrow', color: 'gray'}
]; satelliteObjects = []; relations = [];
		
          var musterErgebnis = "";

          expect(setNodesAndEdges(undefined, nodes, edges, satelliteObjects, relations, 0, 10, 0, 0, true)).toEqual(musterErgebnis);
      });
	  
	        it("MoHol Solution 10 [100, 50, 0, 0]", function () {
		
		  var nodes = [{id: '14', label: 'Check feasibility', type: 'function', group: 'MoHoL Solution 10', color: '#80ff80', level: 1}
,{id: '1', label: 'xor', type: 'operator', group: 'MoHoL Solution 10', color: 'gray', level: 2}
,{id: '7', label: 'Inquiry not feasible', type: 'event', group: 'MoHoL Solution 10', color: '#FF8080', level: 3}
,{id: '11', label: 'xor', type: 'operator', group: 'MoHoL Solution 10', color: 'gray', level: 4}
,{id: '15', label: 'Create rejection', type: 'function', group: 'MoHoL Solution 10', color: '#80ff80', level: 5}
,{id: '8', label: 'xor', type: 'operator', group: 'MoHoL Solution 10', color: 'gray', level: 6}
,{id: '4', label: 'Inform customer subsequently', type: 'function', group: 'MoHoL Solution 10', color: '#80ff80', level: 7}
,{id: '5', label: 'Inquiry possibly feasible', type: 'event', group: 'MoHoL Solution 10', color: '#FF8080', level: 3}
,{id: '9', label: 'Bring about clarification', type: 'function', group: 'MoHoL Solution 10', color: '#80ff80', level: 4}
,{id: '3', label: 'xor', type: 'operator', group: 'MoHoL Solution 10', color: 'gray', level: 5}
,{id: '6', label: 'Clarification positive', type: 'event', group: 'MoHoL Solution 10', color: '#FF8080', level: 6}
,{id: '12', label: 'xor', type: 'operator', group: 'MoHoL Solution 10', color: 'gray', level: 7}
,{id: '2', label: 'Create offer', type: 'function', group: 'MoHoL Solution 10', color: '#80ff80', level: 8}
,{id: '10', label: 'Clarification negative', type: 'event', group: 'MoHoL Solution 10', color: '#FF8080', level: 6}
,{id: '13', label: 'Inquiry feasible', type: 'event', group: 'MoHoL Solution 10', color: '#FF8080', level: 3}
]; edges = [{from: '1', to: '7', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '1', to: '5', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '1', to: '13', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '2', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '3', to: '6', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '3', to: '10', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '5', to: '9', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '6', to: '12', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '7', to: '11', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '8', to: '4', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '9', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '10', to: '11', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '11', to: '15', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '12', to: '2', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '13', to: '12', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '14', to: '1', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '15', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
]; satelliteObjects = []; relations = [];
		
          var musterErgebnis = "";

          expect(setNodesAndEdges(undefined, nodes, edges, satelliteObjects, relations, 100, 50, 0, 0, true)).toEqual(musterErgebnis);
      });
	
	
	
      it("MoHol Solution 10 [0, 10, 0, 0]", function () {
		
		  var  nodes = [{id: '14', label: 'Check feasibility', type: 'function', group: 'MoHoL Solution 10', color: '#80ff80', level: 1}
,{id: '1', label: 'xor', type: 'operator', group: 'MoHoL Solution 10', color: 'gray', level: 2}
,{id: '7', label: 'Inquiry not feasible', type: 'event', group: 'MoHoL Solution 10', color: '#FF8080', level: 3}
,{id: '11', label: 'xor', type: 'operator', group: 'MoHoL Solution 10', color: 'gray', level: 4}
,{id: '15', label: 'Create rejection', type: 'function', group: 'MoHoL Solution 10', color: '#80ff80', level: 5}
,{id: '8', label: 'xor', type: 'operator', group: 'MoHoL Solution 10', color: 'gray', level: 6}
,{id: '4', label: 'Inform customer subsequently', type: 'function', group: 'MoHoL Solution 10', color: '#80ff80', level: 7}
,{id: '5', label: 'Inquiry possibly feasible', type: 'event', group: 'MoHoL Solution 10', color: '#FF8080', level: 3}
,{id: '9', label: 'Bring about clarification', type: 'function', group: 'MoHoL Solution 10', color: '#80ff80', level: 4}
,{id: '3', label: 'xor', type: 'operator', group: 'MoHoL Solution 10', color: 'gray', level: 5}
,{id: '6', label: 'Clarification positive', type: 'event', group: 'MoHoL Solution 10', color: '#FF8080', level: 6}
,{id: '12', label: 'xor', type: 'operator', group: 'MoHoL Solution 10', color: 'gray', level: 7}
,{id: '2', label: 'Create offer', type: 'function', group: 'MoHoL Solution 10', color: '#80ff80', level: 8}
,{id: '10', label: 'Clarification negative', type: 'event', group: 'MoHoL Solution 10', color: '#FF8080', level: 6}
,{id: '13', label: 'Inquiry feasible', type: 'event', group: 'MoHoL Solution 10', color: '#FF8080', level: 3}
]; edges = [{from: '1', to: '7', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '1', to: '5', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '1', to: '13', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '2', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '3', to: '6', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '3', to: '10', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '5', to: '9', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '6', to: '12', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '7', to: '11', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '8', to: '4', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '9', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '10', to: '11', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '11', to: '15', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '12', to: '2', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '13', to: '12', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '14', to: '1', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '15', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
]; satelliteObjects = []; relations = [];
		
          var musterErgebnis = "";

          expect(setNodesAndEdges(undefined, nodes, edges, satelliteObjects, relations, 0, 10, 0, 0, true)).toEqual(musterErgebnis);
      });
	  
	        it("Exams Solution 1 [100, 50, 0, 0]", function () {
		
		  var nodes = [{id: '19', label: 'Business trip necessary', type: 'event', group: 'Solution_1', color: '#FF8080', level: 1}
,{id: '4', label: 'Make application for business trip', type: 'function', group: 'Solution_1', color: '#80ff80', level: 2}
,{id: '17', label: 'Submit application for approval', type: 'function', group: 'Solution_1', color: '#80ff80', level: 3}
,{id: '7', label: 'xor', type: 'operator', group: 'Solution_1', color: 'gray', level: 4}
,{id: '2', label: 'Application approved', type: 'event', group: 'Solution_1', color: '#FF8080', level: 5}
,{id: '6', label: 'Send approved application to initiator\'s post box', type: 'function', group: 'Solution_1', color: '#80ff80', level: 6}
,{id: '1', label: 'Car is needed', type: 'event', group: 'Solution_1', color: '#FF8080', level: 7}
,{id: '9', label: 'Ask whether company car is available', type: 'function', group: 'Solution_1', color: '#80ff80', level: 8}
,{id: '15', label: 'xor', type: 'operator', group: 'Solution_1', color: 'gray', level: 9}
,{id: '16', label: 'Company car available', type: 'event', group: 'Solution_1', color: '#FF8080', level: 10}
,{id: '5', label: 'Book car', type: 'function', group: 'Solution_1', color: '#80ff80', level: 11}
,{id: '8', label: 'Business trip realized', type: 'function', group: 'Solution_1', color: '#80ff80', level: 12}
,{id: '12', label: 'Accounting takes place', type: 'function', group: 'Solution_1', color: '#80ff80', level: 13}
,{id: '13', label: 'Company car not available', type: 'event', group: 'Solution_1', color: '#FF8080', level: 10}
,{id: '14', label: 'Order a rental car', type: 'function', group: 'Solution_1', color: '#80ff80', level: 11}
,{id: '3', label: 'Application not approved', type: 'event', group: 'Solution_1', color: '#FF8080', level: 5}
,{id: '10', label: 'Check whether to discard trip', type: 'function', group: 'Solution_1', color: '#80ff80', level: 6}
,{id: '11', label: 'xor', type: 'operator', group: 'Solution_1', color: 'gray', level: 7}
,{id: '18', label: 'Integrate into another business trip', type: 'function', group: 'Solution_1', color: '#80ff80', level: 8}
]; edges = [{from: '1', to: '9', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '2', to: '6', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '3', to: '10', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '4', to: '17', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '5', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '6', to: '1', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '7', to: '2', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '7', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '8', to: '12', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '9', to: '15', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '10', to: '11', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '11', to: '17', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '11', to: '18', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '13', to: '14', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '14', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '15', to: '16', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '15', to: '13', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '16', to: '5', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '17', to: '7', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '19', to: '4', arrows:'to', style: 'arrow', color: 'gray'}
]; satelliteObjects = []; relations = [];
		
          var musterErgebnis = "";

          expect(setNodesAndEdges(undefined, nodes, edges, satelliteObjects, relations, 100, 50, 0, 0, true)).toEqual(musterErgebnis);
      });
	
	
	
      it("Exams Solution 1 [0, 10, 0, 0]", function () {
		
		  var  nodes = [{id: '19', label: 'Business trip necessary', type: 'event', group: 'Solution_1', color: '#FF8080', level: 1}
,{id: '4', label: 'Make application for business trip', type: 'function', group: 'Solution_1', color: '#80ff80', level: 2}
,{id: '17', label: 'Submit application for approval', type: 'function', group: 'Solution_1', color: '#80ff80', level: 3}
,{id: '7', label: 'xor', type: 'operator', group: 'Solution_1', color: 'gray', level: 4}
,{id: '2', label: 'Application approved', type: 'event', group: 'Solution_1', color: '#FF8080', level: 5}
,{id: '6', label: 'Send approved application to initiator\'s post box', type: 'function', group: 'Solution_1', color: '#80ff80', level: 6}
,{id: '1', label: 'Car is needed', type: 'event', group: 'Solution_1', color: '#FF8080', level: 7}
,{id: '9', label: 'Ask whether company car is available', type: 'function', group: 'Solution_1', color: '#80ff80', level: 8}
,{id: '15', label: 'xor', type: 'operator', group: 'Solution_1', color: 'gray', level: 9}
,{id: '16', label: 'Company car available', type: 'event', group: 'Solution_1', color: '#FF8080', level: 10}
,{id: '5', label: 'Book car', type: 'function', group: 'Solution_1', color: '#80ff80', level: 11}
,{id: '8', label: 'Business trip realized', type: 'function', group: 'Solution_1', color: '#80ff80', level: 12}
,{id: '12', label: 'Accounting takes place', type: 'function', group: 'Solution_1', color: '#80ff80', level: 13}
,{id: '13', label: 'Company car not available', type: 'event', group: 'Solution_1', color: '#FF8080', level: 10}
,{id: '14', label: 'Order a rental car', type: 'function', group: 'Solution_1', color: '#80ff80', level: 11}
,{id: '3', label: 'Application not approved', type: 'event', group: 'Solution_1', color: '#FF8080', level: 5}
,{id: '10', label: 'Check whether to discard trip', type: 'function', group: 'Solution_1', color: '#80ff80', level: 6}
,{id: '11', label: 'xor', type: 'operator', group: 'Solution_1', color: 'gray', level: 7}
,{id: '18', label: 'Integrate into another business trip', type: 'function', group: 'Solution_1', color: '#80ff80', level: 8}
]; edges = [{from: '1', to: '9', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '2', to: '6', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '3', to: '10', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '4', to: '17', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '5', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '6', to: '1', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '7', to: '2', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '7', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '8', to: '12', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '9', to: '15', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '10', to: '11', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '11', to: '17', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '11', to: '18', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '13', to: '14', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '14', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '15', to: '16', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '15', to: '13', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '16', to: '5', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '17', to: '7', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '19', to: '4', arrows:'to', style: 'arrow', color: 'gray'}
]; satelliteObjects = []; relations = [];
		
          var musterErgebnis = "";

          expect(setNodesAndEdges(undefined, nodes, edges, satelliteObjects, relations, 0, 10, 0, 0, true)).toEqual(musterErgebnis);
      });
	  
	        it("Exams Solution 3 [100, 50, 0, 0]", function () {
		
		  var nodes = [{id: '14', label: 'Business trip received', type: 'event', group: 'Solution_3', color: '#FF8080', level: 1}
,{id: '31', label: 'Handed to secretary office', type: 'function', group: 'Solution_3', color: '#80ff80', level: 2}
,{id: '6', label: 'Request placed to secretary', type: 'event', group: 'Solution_3', color: '#FF8080', level: 3}
,{id: '28', label: 'Checked request', type: 'function', group: 'Solution_3', color: '#80ff80', level: 4}
,{id: '15', label: 'Note to manager sent', type: 'event', group: 'Solution_3', color: '#FF8080', level: 5}
,{id: '3', label: 'Checked note', type: 'function', group: 'Solution_3', color: '#80ff80', level: 6}
,{id: '18', label: 'xor', type: 'operator', group: 'Solution_3', color: 'gray', level: 7}
,{id: '12', label: 'Accepted trip', type: 'event', group: 'Solution_3', color: '#FF8080', level: 8}
,{id: '5', label: 'File prepared', type: 'function', group: 'Solution_3', color: '#80ff80', level: 9}
,{id: '34', label: 'xor', type: 'operator', group: 'Solution_3', color: 'gray', level: 10}
,{id: '29', label: 'Email sent', type: 'event', group: 'Solution_3', color: '#FF8080', level: 11}
,{id: '32', label: 'Checked email', type: 'function', group: 'Solution_3', color: '#80ff80', level: 12}
,{id: '30', label: 'xor', type: 'operator', group: 'Solution_3', color: 'gray', level: 13}
,{id: '20', label: 'Accepted Email', type: 'event', group: 'Solution_3', color: '#FF8080', level: 14}
,{id: '23', label: 'Car checked', type: 'function', group: 'Solution_3', color: '#80ff80', level: 15}
,{id: '8', label: 'or', type: 'operator', group: 'Solution_3', color: 'gray', level: 16}
,{id: '11', label: 'Not available car', type: 'event', group: 'Solution_3', color: '#FF8080', level: 17}
,{id: '33', label: 'Rental car ordered', type: 'function', group: 'Solution_3', color: '#80ff80', level: 18}
,{id: '17', label: 'or', type: 'operator', group: 'Solution_3', color: 'gray', level: 19}
,{id: '25', label: 'Accounting realized', type: 'event', group: 'Solution_3', color: '#FF8080', level: 20}
,{id: '19', label: 'xor', type: 'operator', group: 'Solution_3', color: 'gray', level: 21}
,{id: '1', label: 'Action took', type: 'function', group: 'Solution_3', color: '#80ff80', level: 22}
,{id: '36', label: 'Available car', type: 'event', group: 'Solution_3', color: '#FF8080', level: 17}
,{id: '9', label: 'Car booked', type: 'function', group: 'Solution_3', color: '#80ff80', level: 18}
,{id: '24', label: 'Rejected email', type: 'event', group: 'Solution_3', color: '#FF8080', level: 14}
,{id: '22', label: 'Trip checked', type: 'function', group: 'Solution_3', color: '#80ff80', level: 15}
,{id: '10', label: 'or', type: 'operator', group: 'Solution_3', color: 'gray', level: 16}
,{id: '16', label: 'Trip discarded', type: 'event', group: 'Solution_3', color: '#FF8080', level: 17}
,{id: '2', label: 'Integrate with oder', type: 'function', group: 'Solution_3', color: '#80ff80', level: 18}
,{id: '27', label: 'or', type: 'operator', group: 'Solution_3', color: 'gray', level: 19}
,{id: '21', label: 'Request handed again', type: 'event', group: 'Solution_3', color: '#FF8080', level: 20}
,{id: '13', label: 'Trip reasoned', type: 'event', group: 'Solution_3', color: '#FF8080', level: 17}
,{id: '4', label: 'Done reasoning', type: 'function', group: 'Solution_3', color: '#80ff80', level: 18}
,{id: '7', label: 'Rejected trip', type: 'event', group: 'Solution_3', color: '#FF8080', level: 8}
,{id: '26', label: 'No file prepared', type: 'function', group: 'Solution_3', color: '#80ff80', level: 9}
,{id: '35', label: 'Done decision', type: 'event', group: 'Solution_3', color: '#FF8080', level: 1}
]; edges = [{from: '2', to: '27', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '3', to: '18', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '4', to: '27', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '5', to: '34', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '6', to: '28', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '7', to: '26', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '8', to: '11', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '8', to: '36', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '9', to: '17', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '10', to: '16', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '10', to: '13', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '11', to: '33', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '12', to: '5', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '13', to: '4', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '14', to: '31', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '15', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '16', to: '2', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '17', to: '25', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '18', to: '12', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '18', to: '7', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '19', to: '1', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '20', to: '23', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '21', to: '19', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '22', to: '10', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '23', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '24', to: '22', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '25', to: '19', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '26', to: '34', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '27', to: '21', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '28', to: '15', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '29', to: '32', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '30', to: '20', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '30', to: '24', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '31', to: '6', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '32', to: '30', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '33', to: '17', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '34', to: '29', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '36', to: '9', arrows:'to', style: 'arrow', color: 'gray'}
]; satelliteObjects = []; relations = [];
		
          var musterErgebnis = "";

          expect(setNodesAndEdges(undefined, nodes, edges, satelliteObjects, relations, 100, 50, 0, 0, true)).toEqual(musterErgebnis);
      });
	
	
	
      it("Exams Solution 3 [0, 10, 0, 0]", function () {
		
		  var  nodes = [{id: '14', label: 'Business trip received', type: 'event', group: 'Solution_3', color: '#FF8080', level: 1}
,{id: '31', label: 'Handed to secretary office', type: 'function', group: 'Solution_3', color: '#80ff80', level: 2}
,{id: '6', label: 'Request placed to secretary', type: 'event', group: 'Solution_3', color: '#FF8080', level: 3}
,{id: '28', label: 'Checked request', type: 'function', group: 'Solution_3', color: '#80ff80', level: 4}
,{id: '15', label: 'Note to manager sent', type: 'event', group: 'Solution_3', color: '#FF8080', level: 5}
,{id: '3', label: 'Checked note', type: 'function', group: 'Solution_3', color: '#80ff80', level: 6}
,{id: '18', label: 'xor', type: 'operator', group: 'Solution_3', color: 'gray', level: 7}
,{id: '12', label: 'Accepted trip', type: 'event', group: 'Solution_3', color: '#FF8080', level: 8}
,{id: '5', label: 'File prepared', type: 'function', group: 'Solution_3', color: '#80ff80', level: 9}
,{id: '34', label: 'xor', type: 'operator', group: 'Solution_3', color: 'gray', level: 10}
,{id: '29', label: 'Email sent', type: 'event', group: 'Solution_3', color: '#FF8080', level: 11}
,{id: '32', label: 'Checked email', type: 'function', group: 'Solution_3', color: '#80ff80', level: 12}
,{id: '30', label: 'xor', type: 'operator', group: 'Solution_3', color: 'gray', level: 13}
,{id: '20', label: 'Accepted Email', type: 'event', group: 'Solution_3', color: '#FF8080', level: 14}
,{id: '23', label: 'Car checked', type: 'function', group: 'Solution_3', color: '#80ff80', level: 15}
,{id: '8', label: 'or', type: 'operator', group: 'Solution_3', color: 'gray', level: 16}
,{id: '11', label: 'Not available car', type: 'event', group: 'Solution_3', color: '#FF8080', level: 17}
,{id: '33', label: 'Rental car ordered', type: 'function', group: 'Solution_3', color: '#80ff80', level: 18}
,{id: '17', label: 'or', type: 'operator', group: 'Solution_3', color: 'gray', level: 19}
,{id: '25', label: 'Accounting realized', type: 'event', group: 'Solution_3', color: '#FF8080', level: 20}
,{id: '19', label: 'xor', type: 'operator', group: 'Solution_3', color: 'gray', level: 21}
,{id: '1', label: 'Action took', type: 'function', group: 'Solution_3', color: '#80ff80', level: 22}
,{id: '36', label: 'Available car', type: 'event', group: 'Solution_3', color: '#FF8080', level: 17}
,{id: '9', label: 'Car booked', type: 'function', group: 'Solution_3', color: '#80ff80', level: 18}
,{id: '24', label: 'Rejected email', type: 'event', group: 'Solution_3', color: '#FF8080', level: 14}
,{id: '22', label: 'Trip checked', type: 'function', group: 'Solution_3', color: '#80ff80', level: 15}
,{id: '10', label: 'or', type: 'operator', group: 'Solution_3', color: 'gray', level: 16}
,{id: '16', label: 'Trip discarded', type: 'event', group: 'Solution_3', color: '#FF8080', level: 17}
,{id: '2', label: 'Integrate with oder', type: 'function', group: 'Solution_3', color: '#80ff80', level: 18}
,{id: '27', label: 'or', type: 'operator', group: 'Solution_3', color: 'gray', level: 19}
,{id: '21', label: 'Request handed again', type: 'event', group: 'Solution_3', color: '#FF8080', level: 20}
,{id: '13', label: 'Trip reasoned', type: 'event', group: 'Solution_3', color: '#FF8080', level: 17}
,{id: '4', label: 'Done reasoning', type: 'function', group: 'Solution_3', color: '#80ff80', level: 18}
,{id: '7', label: 'Rejected trip', type: 'event', group: 'Solution_3', color: '#FF8080', level: 8}
,{id: '26', label: 'No file prepared', type: 'function', group: 'Solution_3', color: '#80ff80', level: 9}
,{id: '35', label: 'Done decision', type: 'event', group: 'Solution_3', color: '#FF8080', level: 1}
]; edges = [{from: '2', to: '27', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '3', to: '18', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '4', to: '27', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '5', to: '34', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '6', to: '28', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '7', to: '26', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '8', to: '11', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '8', to: '36', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '9', to: '17', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '10', to: '16', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '10', to: '13', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '11', to: '33', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '12', to: '5', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '13', to: '4', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '14', to: '31', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '15', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '16', to: '2', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '17', to: '25', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '18', to: '12', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '18', to: '7', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '19', to: '1', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '20', to: '23', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '21', to: '19', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '22', to: '10', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '23', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '24', to: '22', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '25', to: '19', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '26', to: '34', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '27', to: '21', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '28', to: '15', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '29', to: '32', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '30', to: '20', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '30', to: '24', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '31', to: '6', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '32', to: '30', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '33', to: '17', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '34', to: '29', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '36', to: '9', arrows:'to', style: 'arrow', color: 'gray'}
]; satelliteObjects = []; relations = [];
		
          var musterErgebnis = "";

          expect(setNodesAndEdges(undefined, nodes, edges, satelliteObjects, relations, 0, 10, 0, 0, true)).toEqual(musterErgebnis);
      });
	  
	        it("Exams Solution 4 [100, 50, 0, 0]", function () {
		
		  var nodes = [{id: '6', label: 'Business Trip Application Required', type: 'event', group: 'Solution_4', color: '#FF8080', level: 1}
,{id: '1', label: 'Fill Application', type: 'function', group: 'Solution_4', color: '#80ff80', level: 2}
,{id: '27', label: 'or', type: 'operator', group: 'Solution_4', color: 'gray', level: 3}
,{id: '24', label: 'Application completed', type: 'event', group: 'Solution_4', color: '#FF8080', level: 4}
,{id: '18', label: 'Handing application', type: 'function', group: 'Solution_4', color: '#80ff80', level: 5}
,{id: '13', label: 'Application given', type: 'event', group: 'Solution_4', color: '#FF8080', level: 6}
,{id: '40', label: 'Review of application', type: 'function', group: 'Solution_4', color: '#80ff80', level: 7}
,{id: '37', label: 'xor', type: 'operator', group: 'Solution_4', color: 'gray', level: 8}
,{id: '34', label: 'Application doesn\'t match requirements', type: 'event', group: 'Solution_4', color: '#FF8080', level: 9}
,{id: '5', label: 'xor', type: 'operator', group: 'Solution_4', color: 'gray', level: 10}
,{id: '41', label: 'Match noting', type: 'function', group: 'Solution_4', color: '#80ff80', level: 11}
,{id: '36', label: 'Noted', type: 'event', group: 'Solution_4', color: '#FF8080', level: 12}
,{id: '28', label: 'Application sent to manager', type: 'function', group: 'Solution_4', color: '#80ff80', level: 13}
,{id: '8', label: 'xor', type: 'operator', group: 'Solution_4', color: 'gray', level: 14}
,{id: '32', label: 'Approved', type: 'event', group: 'Solution_4', color: '#FF8080', level: 15}
,{id: '9', label: 'Noting employee and trip period', type: 'function', group: 'Solution_4', color: '#80ff80', level: 16}
,{id: '26', label: 'Trip registered', type: 'event', group: 'Solution_4', color: '#FF8080', level: 17}
,{id: '35', label: 'xor', type: 'operator', group: 'Solution_4', color: 'gray', level: 18}
,{id: '16', label: 'Inform employee', type: 'function', group: 'Solution_4', color: '#80ff80', level: 19}
,{id: '17', label: 'Rejected', type: 'event', group: 'Solution_4', color: '#FF8080', level: 20}
,{id: '15', label: 'Review decision', type: 'function', group: 'Solution_4', color: '#80ff80', level: 21}
,{id: '33', label: 'xor', type: 'operator', group: 'Solution_4', color: 'gray', level: 22}
,{id: '23', label: 'Trip rejected', type: 'event', group: 'Solution_4', color: '#FF8080', level: 23}
,{id: '30', label: 'Revise application', type: 'function', group: 'Solution_4', color: '#80ff80', level: 24}
,{id: '25', label: 'Trip approved', type: 'event', group: 'Solution_4', color: '#FF8080', level: 23}
,{id: '31', label: 'Approved', type: 'event', group: 'Solution_4', color: '#FF8080', level: 20}
,{id: '29', label: 'Check availability of car', type: 'function', group: 'Solution_4', color: '#80ff80', level: 21}
,{id: '10', label: 'xor', type: 'operator', group: 'Solution_4', color: 'gray', level: 22}
,{id: '7', label: 'Car available', type: 'event', group: 'Solution_4', color: '#FF8080', level: 23}
,{id: '43', label: 'Booking Company Car', type: 'function', group: 'Solution_4', color: '#80ff80', level: 24}
,{id: '22', label: 'company car booked', type: 'event', group: 'Solution_4', color: '#FF8080', level: 25}
,{id: '12', label: 'xor', type: 'operator', group: 'Solution_4', color: 'gray', level: 26}
,{id: '38', label: 'and', type: 'operator', group: 'Solution_4', color: 'gray', level: 27}
,{id: '14', label: 'Realize Trip', type: 'function', group: 'Solution_4', color: '#80ff80', level: 28}
,{id: '21', label: 'Realized Trip', type: 'event', group: 'Solution_4', color: '#FF8080', level: 29}
,{id: '39', label: 'Accounting for Trip', type: 'function', group: 'Solution_4', color: '#80ff80', level: 30}
,{id: '3', label: 'Trip accounted', type: 'event', group: 'Solution_4', color: '#FF8080', level: 31}
,{id: '42', label: 'Car not available', type: 'event', group: 'Solution_4', color: '#FF8080', level: 23}
,{id: '4', label: 'Booking rental car', type: 'function', group: 'Solution_4', color: '#80ff80', level: 24}
,{id: '20', label: 'Rental car booked', type: 'event', group: 'Solution_4', color: '#FF8080', level: 25}
,{id: '19', label: 'Rejected', type: 'event', group: 'Solution_4', color: '#FF8080', level: 15}
,{id: '2', label: 'Application matches requirements', type: 'event', group: 'Solution_4', color: '#FF8080', level: 9}
,{id: '11', label: 'Car available', type: 'event', group: 'Solution_4', color: '#FF8080', level: 1}
]; edges = [{from: '1', to: '27', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '2', to: '5', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '4', to: '20', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '5', to: '41', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '6', to: '1', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '7', to: '43', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '8', to: '32', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '8', to: '19', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '9', to: '26', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '10', to: '7', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '10', to: '42', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '11', to: '38', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '12', to: '38', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '13', to: '40', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '14', to: '21', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '15', to: '33', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '16', to: '17', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '16', to: '31', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '17', to: '15', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '18', to: '13', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '19', to: '35', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '20', to: '12', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '21', to: '39', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '22', to: '12', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '23', to: '30', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '24', to: '18', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '26', to: '35', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '27', to: '24', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '28', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '29', to: '10', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '31', to: '29', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '32', to: '9', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '33', to: '23', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '33', to: '25', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '34', to: '5', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '35', to: '16', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '36', to: '28', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '37', to: '34', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '37', to: '2', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '38', to: '14', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '39', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '40', to: '37', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '41', to: '36', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '42', to: '4', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '43', to: '22', arrows:'to', style: 'arrow', color: 'gray'}
]; satelliteObjects = []; relations = [];
		
          var musterErgebnis = "";

          expect(setNodesAndEdges(undefined, nodes, edges, satelliteObjects, relations, 100, 50, 0, 0, true)).toEqual(musterErgebnis);
      });
	
	
	
      it("Exams Solution 4 [0, 10, 0, 0]", function () {
		
		  var  nodes = [{id: '6', label: 'Business Trip Application Required', type: 'event', group: 'Solution_4', color: '#FF8080', level: 1}
,{id: '1', label: 'Fill Application', type: 'function', group: 'Solution_4', color: '#80ff80', level: 2}
,{id: '27', label: 'or', type: 'operator', group: 'Solution_4', color: 'gray', level: 3}
,{id: '24', label: 'Application completed', type: 'event', group: 'Solution_4', color: '#FF8080', level: 4}
,{id: '18', label: 'Handing application', type: 'function', group: 'Solution_4', color: '#80ff80', level: 5}
,{id: '13', label: 'Application given', type: 'event', group: 'Solution_4', color: '#FF8080', level: 6}
,{id: '40', label: 'Review of application', type: 'function', group: 'Solution_4', color: '#80ff80', level: 7}
,{id: '37', label: 'xor', type: 'operator', group: 'Solution_4', color: 'gray', level: 8}
,{id: '34', label: 'Application doesn\'t match requirements', type: 'event', group: 'Solution_4', color: '#FF8080', level: 9}
,{id: '5', label: 'xor', type: 'operator', group: 'Solution_4', color: 'gray', level: 10}
,{id: '41', label: 'Match noting', type: 'function', group: 'Solution_4', color: '#80ff80', level: 11}
,{id: '36', label: 'Noted', type: 'event', group: 'Solution_4', color: '#FF8080', level: 12}
,{id: '28', label: 'Application sent to manager', type: 'function', group: 'Solution_4', color: '#80ff80', level: 13}
,{id: '8', label: 'xor', type: 'operator', group: 'Solution_4', color: 'gray', level: 14}
,{id: '32', label: 'Approved', type: 'event', group: 'Solution_4', color: '#FF8080', level: 15}
,{id: '9', label: 'Noting employee and trip period', type: 'function', group: 'Solution_4', color: '#80ff80', level: 16}
,{id: '26', label: 'Trip registered', type: 'event', group: 'Solution_4', color: '#FF8080', level: 17}
,{id: '35', label: 'xor', type: 'operator', group: 'Solution_4', color: 'gray', level: 18}
,{id: '16', label: 'Inform employee', type: 'function', group: 'Solution_4', color: '#80ff80', level: 19}
,{id: '17', label: 'Rejected', type: 'event', group: 'Solution_4', color: '#FF8080', level: 20}
,{id: '15', label: 'Review decision', type: 'function', group: 'Solution_4', color: '#80ff80', level: 21}
,{id: '33', label: 'xor', type: 'operator', group: 'Solution_4', color: 'gray', level: 22}
,{id: '23', label: 'Trip rejected', type: 'event', group: 'Solution_4', color: '#FF8080', level: 23}
,{id: '30', label: 'Revise application', type: 'function', group: 'Solution_4', color: '#80ff80', level: 24}
,{id: '25', label: 'Trip approved', type: 'event', group: 'Solution_4', color: '#FF8080', level: 23}
,{id: '31', label: 'Approved', type: 'event', group: 'Solution_4', color: '#FF8080', level: 20}
,{id: '29', label: 'Check availability of car', type: 'function', group: 'Solution_4', color: '#80ff80', level: 21}
,{id: '10', label: 'xor', type: 'operator', group: 'Solution_4', color: 'gray', level: 22}
,{id: '7', label: 'Car available', type: 'event', group: 'Solution_4', color: '#FF8080', level: 23}
,{id: '43', label: 'Booking Company Car', type: 'function', group: 'Solution_4', color: '#80ff80', level: 24}
,{id: '22', label: 'company car booked', type: 'event', group: 'Solution_4', color: '#FF8080', level: 25}
,{id: '12', label: 'xor', type: 'operator', group: 'Solution_4', color: 'gray', level: 26}
,{id: '38', label: 'and', type: 'operator', group: 'Solution_4', color: 'gray', level: 27}
,{id: '14', label: 'Realize Trip', type: 'function', group: 'Solution_4', color: '#80ff80', level: 28}
,{id: '21', label: 'Realized Trip', type: 'event', group: 'Solution_4', color: '#FF8080', level: 29}
,{id: '39', label: 'Accounting for Trip', type: 'function', group: 'Solution_4', color: '#80ff80', level: 30}
,{id: '3', label: 'Trip accounted', type: 'event', group: 'Solution_4', color: '#FF8080', level: 31}
,{id: '42', label: 'Car not available', type: 'event', group: 'Solution_4', color: '#FF8080', level: 23}
,{id: '4', label: 'Booking rental car', type: 'function', group: 'Solution_4', color: '#80ff80', level: 24}
,{id: '20', label: 'Rental car booked', type: 'event', group: 'Solution_4', color: '#FF8080', level: 25}
,{id: '19', label: 'Rejected', type: 'event', group: 'Solution_4', color: '#FF8080', level: 15}
,{id: '2', label: 'Application matches requirements', type: 'event', group: 'Solution_4', color: '#FF8080', level: 9}
,{id: '11', label: 'Car available', type: 'event', group: 'Solution_4', color: '#FF8080', level: 1}
]; edges = [{from: '1', to: '27', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '2', to: '5', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '4', to: '20', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '5', to: '41', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '6', to: '1', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '7', to: '43', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '8', to: '32', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '8', to: '19', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '9', to: '26', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '10', to: '7', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '10', to: '42', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '11', to: '38', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '12', to: '38', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '13', to: '40', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '14', to: '21', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '15', to: '33', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '16', to: '17', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '16', to: '31', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '17', to: '15', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '18', to: '13', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '19', to: '35', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '20', to: '12', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '21', to: '39', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '22', to: '12', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '23', to: '30', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '24', to: '18', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '26', to: '35', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '27', to: '24', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '28', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '29', to: '10', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '31', to: '29', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '32', to: '9', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '33', to: '23', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '33', to: '25', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '34', to: '5', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '35', to: '16', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '36', to: '28', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '37', to: '34', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '37', to: '2', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '38', to: '14', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '39', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '40', to: '37', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '41', to: '36', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '42', to: '4', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '43', to: '22', arrows:'to', style: 'arrow', color: 'gray'}
]; satelliteObjects = []; relations = [];
		
          var musterErgebnis = "";

          expect(setNodesAndEdges(undefined, nodes, edges, satelliteObjects, relations, 0, 10, 0, 0, true)).toEqual(musterErgebnis);
      });
	  
	  
	        it("Exams Solution 5 [100, 50, 0, 0]", function () {
		
		  var nodes = [{id: '8', label: 'Application for trip needed', type: 'event', group: 'Solution_5', color: '#FF8080', level: 1}
,{id: '39', label: 'Making Application for trip', type: 'function', group: 'Solution_5', color: '#80ff80', level: 2}
,{id: '29', label: 'Application made', type: 'event', group: 'Solution_5', color: '#FF8080', level: 3}
,{id: '7', label: 'Handing in application', type: 'function', group: 'Solution_5', color: '#80ff80', level: 4}
,{id: '21', label: 'application handed over', type: 'event', group: 'Solution_5', color: '#FF8080', level: 5}
,{id: '10', label: 'Checking Application', type: 'function', group: 'Solution_5', color: '#80ff80', level: 6}
,{id: '3', label: 'xor', type: 'operator', group: 'Solution_5', color: 'gray', level: 7}
,{id: '12', label: 'Not according to requirements', type: 'event', group: 'Solution_5', color: '#FF8080', level: 8}
,{id: '11', label: 'xor', type: 'operator', group: 'Solution_5', color: 'gray', level: 9}
,{id: '4', label: 'Analyzing the app', type: 'function', group: 'Solution_5', color: '#80ff80', level: 10}
,{id: '24', label: 'Requirements matched', type: 'event', group: 'Solution_5', color: '#FF8080', level: 11}
,{id: '25', label: 'Handing in the note at manager\'s office', type: 'function', group: 'Solution_5', color: '#80ff80', level: 12}
,{id: '20', label: 'xor', type: 'operator', group: 'Solution_5', color: 'gray', level: 13}
,{id: '15', label: 'App rejected', type: 'event', group: 'Solution_5', color: '#FF8080', level: 14}
,{id: '43', label: 'xor', type: 'operator', group: 'Solution_5', color: 'gray', level: 15}
,{id: '40', label: 'Notifying Employee', type: 'function', group: 'Solution_5', color: '#80ff80', level: 16}
,{id: '1', label: 'xor', type: 'operator', group: 'Solution_5', color: 'gray', level: 17}
,{id: '37', label: 'Trip approved', type: 'event', group: 'Solution_5', color: '#FF8080', level: 18}
,{id: '5', label: 'Checking car\'s availability', type: 'function', group: 'Solution_5', color: '#80ff80', level: 19}
,{id: '19', label: 'xor', type: 'operator', group: 'Solution_5', color: 'gray', level: 20}
,{id: '22', label: 'Car is not available', type: 'event', group: 'Solution_5', color: '#FF8080', level: 21}
,{id: '33', label: 'Booking rental car', type: 'function', group: 'Solution_5', color: '#80ff80', level: 22}
,{id: '18', label: 'Booked rental car', type: 'event', group: 'Solution_5', color: '#FF8080', level: 23}
,{id: '31', label: 'xor', type: 'operator', group: 'Solution_5', color: 'gray', level: 24}
,{id: '14', label: 'and', type: 'operator', group: 'Solution_5', color: 'gray', level: 25}
,{id: '34', label: 'Realizing the trip', type: 'function', group: 'Solution_5', color: '#80ff80', level: 26}
,{id: '42', label: 'Trip realized', type: 'event', group: 'Solution_5', color: '#FF8080', level: 27}
,{id: '6', label: 'Accounting taking place', type: 'function', group: 'Solution_5', color: '#80ff80', level: 28}
,{id: '41', label: 'Accounting done', type: 'event', group: 'Solution_5', color: '#FF8080', level: 29}
,{id: '16', label: 'Car is available', type: 'event', group: 'Solution_5', color: '#FF8080', level: 21}
,{id: '13', label: 'Booking co\'s car', type: 'function', group: 'Solution_5', color: '#80ff80', level: 22}
,{id: '17', label: 'Booked Co\'s car', type: 'event', group: 'Solution_5', color: '#FF8080', level: 23}
,{id: '30', label: 'Trip rejected', type: 'event', group: 'Solution_5', color: '#FF8080', level: 18}
,{id: '2', label: 'checking the trip app again', type: 'function', group: 'Solution_5', color: '#80ff80', level: 19}
,{id: '38', label: 'xor', type: 'operator', group: 'Solution_5', color: 'gray', level: 20}
,{id: '27', label: 'Trip not descarded', type: 'event', group: 'Solution_5', color: '#FF8080', level: 21}
,{id: '36', label: 'Integrate it later', type: 'function', group: 'Solution_5', color: '#80ff80', level: 22}
,{id: '9', label: 'Trip descarded', type: 'event', group: 'Solution_5', color: '#FF8080', level: 21}
,{id: '35', label: 'App Accepted', type: 'event', group: 'Solution_5', color: '#FF8080', level: 14}
,{id: '32', label: 'Making note of employee', type: 'function', group: 'Solution_5', color: '#80ff80', level: 15}
,{id: '28', label: 'B-Trip is entered in record', type: 'event', group: 'Solution_5', color: '#FF8080', level: 16}
,{id: '23', label: 'According to requirements', type: 'event', group: 'Solution_5', color: '#FF8080', level: 8}
,{id: '26', label: 'Expiry of trip reached', type: 'event', group: 'Solution_5', color: '#FF8080', level: 1}
]; edges = [{from: '1', to: '37', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '1', to: '30', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '2', to: '38', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '3', to: '12', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '3', to: '23', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '4', to: '24', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '5', to: '19', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '6', to: '41', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '7', to: '21', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '8', to: '39', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '10', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '11', to: '4', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '12', to: '11', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '13', to: '17', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '14', to: '34', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '15', to: '43', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '16', to: '13', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '17', to: '31', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '18', to: '31', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '19', to: '22', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '19', to: '16', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '20', to: '15', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '20', to: '35', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '21', to: '10', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '22', to: '33', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '23', to: '11', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '24', to: '25', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '25', to: '20', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '26', to: '14', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '27', to: '36', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '28', to: '43', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '29', to: '7', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '30', to: '2', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '31', to: '14', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '32', to: '28', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '33', to: '18', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '34', to: '42', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '35', to: '32', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '37', to: '5', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '38', to: '27', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '38', to: '9', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '39', to: '29', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '40', to: '1', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '42', to: '6', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '43', to: '40', arrows:'to', style: 'arrow', color: 'gray'}
]; satelliteObjects = []; relations = [];
		
          var musterErgebnis = "";

          expect(setNodesAndEdges(undefined, nodes, edges, satelliteObjects, relations, 100, 50, 0, 0, true)).toEqual(musterErgebnis);
      });
	
	
	
      it("Exams Solution 5 [0, 10, 0, 0]", function () {
		
		  var  nodes = [{id: '8', label: 'Application for trip needed', type: 'event', group: 'Solution_5', color: '#FF8080', level: 1}
,{id: '39', label: 'Making Application for trip', type: 'function', group: 'Solution_5', color: '#80ff80', level: 2}
,{id: '29', label: 'Application made', type: 'event', group: 'Solution_5', color: '#FF8080', level: 3}
,{id: '7', label: 'Handing in application', type: 'function', group: 'Solution_5', color: '#80ff80', level: 4}
,{id: '21', label: 'application handed over', type: 'event', group: 'Solution_5', color: '#FF8080', level: 5}
,{id: '10', label: 'Checking Application', type: 'function', group: 'Solution_5', color: '#80ff80', level: 6}
,{id: '3', label: 'xor', type: 'operator', group: 'Solution_5', color: 'gray', level: 7}
,{id: '12', label: 'Not according to requirements', type: 'event', group: 'Solution_5', color: '#FF8080', level: 8}
,{id: '11', label: 'xor', type: 'operator', group: 'Solution_5', color: 'gray', level: 9}
,{id: '4', label: 'Analyzing the app', type: 'function', group: 'Solution_5', color: '#80ff80', level: 10}
,{id: '24', label: 'Requirements matched', type: 'event', group: 'Solution_5', color: '#FF8080', level: 11}
,{id: '25', label: 'Handing in the note at manager\'s office', type: 'function', group: 'Solution_5', color: '#80ff80', level: 12}
,{id: '20', label: 'xor', type: 'operator', group: 'Solution_5', color: 'gray', level: 13}
,{id: '15', label: 'App rejected', type: 'event', group: 'Solution_5', color: '#FF8080', level: 14}
,{id: '43', label: 'xor', type: 'operator', group: 'Solution_5', color: 'gray', level: 15}
,{id: '40', label: 'Notifying Employee', type: 'function', group: 'Solution_5', color: '#80ff80', level: 16}
,{id: '1', label: 'xor', type: 'operator', group: 'Solution_5', color: 'gray', level: 17}
,{id: '37', label: 'Trip approved', type: 'event', group: 'Solution_5', color: '#FF8080', level: 18}
,{id: '5', label: 'Checking car\'s availability', type: 'function', group: 'Solution_5', color: '#80ff80', level: 19}
,{id: '19', label: 'xor', type: 'operator', group: 'Solution_5', color: 'gray', level: 20}
,{id: '22', label: 'Car is not available', type: 'event', group: 'Solution_5', color: '#FF8080', level: 21}
,{id: '33', label: 'Booking rental car', type: 'function', group: 'Solution_5', color: '#80ff80', level: 22}
,{id: '18', label: 'Booked rental car', type: 'event', group: 'Solution_5', color: '#FF8080', level: 23}
,{id: '31', label: 'xor', type: 'operator', group: 'Solution_5', color: 'gray', level: 24}
,{id: '14', label: 'and', type: 'operator', group: 'Solution_5', color: 'gray', level: 25}
,{id: '34', label: 'Realizing the trip', type: 'function', group: 'Solution_5', color: '#80ff80', level: 26}
,{id: '42', label: 'Trip realized', type: 'event', group: 'Solution_5', color: '#FF8080', level: 27}
,{id: '6', label: 'Accounting taking place', type: 'function', group: 'Solution_5', color: '#80ff80', level: 28}
,{id: '41', label: 'Accounting done', type: 'event', group: 'Solution_5', color: '#FF8080', level: 29}
,{id: '16', label: 'Car is available', type: 'event', group: 'Solution_5', color: '#FF8080', level: 21}
,{id: '13', label: 'Booking co\'s car', type: 'function', group: 'Solution_5', color: '#80ff80', level: 22}
,{id: '17', label: 'Booked Co\'s car', type: 'event', group: 'Solution_5', color: '#FF8080', level: 23}
,{id: '30', label: 'Trip rejected', type: 'event', group: 'Solution_5', color: '#FF8080', level: 18}
,{id: '2', label: 'checking the trip app again', type: 'function', group: 'Solution_5', color: '#80ff80', level: 19}
,{id: '38', label: 'xor', type: 'operator', group: 'Solution_5', color: 'gray', level: 20}
,{id: '27', label: 'Trip not descarded', type: 'event', group: 'Solution_5', color: '#FF8080', level: 21}
,{id: '36', label: 'Integrate it later', type: 'function', group: 'Solution_5', color: '#80ff80', level: 22}
,{id: '9', label: 'Trip descarded', type: 'event', group: 'Solution_5', color: '#FF8080', level: 21}
,{id: '35', label: 'App Accepted', type: 'event', group: 'Solution_5', color: '#FF8080', level: 14}
,{id: '32', label: 'Making note of employee', type: 'function', group: 'Solution_5', color: '#80ff80', level: 15}
,{id: '28', label: 'B-Trip is entered in record', type: 'event', group: 'Solution_5', color: '#FF8080', level: 16}
,{id: '23', label: 'According to requirements', type: 'event', group: 'Solution_5', color: '#FF8080', level: 8}
,{id: '26', label: 'Expiry of trip reached', type: 'event', group: 'Solution_5', color: '#FF8080', level: 1}
]; edges = [{from: '1', to: '37', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '1', to: '30', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '2', to: '38', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '3', to: '12', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '3', to: '23', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '4', to: '24', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '5', to: '19', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '6', to: '41', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '7', to: '21', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '8', to: '39', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '10', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '11', to: '4', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '12', to: '11', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '13', to: '17', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '14', to: '34', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '15', to: '43', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '16', to: '13', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '17', to: '31', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '18', to: '31', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '19', to: '22', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '19', to: '16', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '20', to: '15', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '20', to: '35', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '21', to: '10', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '22', to: '33', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '23', to: '11', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '24', to: '25', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '25', to: '20', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '26', to: '14', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '27', to: '36', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '28', to: '43', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '29', to: '7', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '30', to: '2', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '31', to: '14', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '32', to: '28', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '33', to: '18', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '34', to: '42', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '35', to: '32', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '37', to: '5', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '38', to: '27', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '38', to: '9', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '39', to: '29', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '40', to: '1', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '42', to: '6', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '43', to: '40', arrows:'to', style: 'arrow', color: 'gray'}
]; satelliteObjects = []; relations = [];
		
          var musterErgebnis = "";

          expect(setNodesAndEdges(undefined, nodes, edges, satelliteObjects, relations, 0, 10, 0, 0, true)).toEqual(musterErgebnis);
      });
	  
	        it("Exams Solution 6 [100, 50, 0, 0]", function () {
		
		  var nodes = [{id: '36', label: 'Date of business trip reached', type: 'event', group: 'Solution_6', color: '#FF8080', level: 1}
,{id: '15', label: 'xor', type: 'operator', group: 'Solution_6', color: 'gray', level: 2}
,{id: '6', label: 'Realizing business trip', type: 'function', group: 'Solution_6', color: '#80ff80', level: 3}
,{id: '22', label: 'Business trip realized', type: 'event', group: 'Solution_6', color: '#FF8080', level: 4}
,{id: '25', label: 'Accounting for business trip takes place', type: 'function', group: 'Solution_6', color: '#80ff80', level: 5}
,{id: '3', label: 'Business trip accounted', type: 'event', group: 'Solution_6', color: '#FF8080', level: 6}
,{id: '39', label: 'Business Trip application need', type: 'event', group: 'Solution_6', color: '#FF8080', level: 1}
,{id: '2', label: 'Application filling', type: 'function', group: 'Solution_6', color: '#80ff80', level: 2}
,{id: '17', label: 'and', type: 'operator', group: 'Solution_6', color: 'gray', level: 3}
,{id: '38', label: 'Application filled', type: 'event', group: 'Solution_6', color: '#FF8080', level: 4}
,{id: '4', label: 'Handing in for approval', type: 'function', group: 'Solution_6', color: '#80ff80', level: 5}
,{id: '14', label: 'Application handed in', type: 'event', group: 'Solution_6', color: '#FF8080', level: 6}
,{id: '29', label: 'Application filling', type: 'function', group: 'Solution_6', color: '#80ff80', level: 7}
,{id: '31', label: 'xor', type: 'operator', group: 'Solution_6', color: 'gray', level: 8}
,{id: '10', label: 'Application as not per the requirements', type: 'event', group: 'Solution_6', color: '#FF8080', level: 9}
,{id: '9', label: 'xor', type: 'operator', group: 'Solution_6', color: 'gray', level: 10}
,{id: '43', label: 'Matching the requirements', type: 'function', group: 'Solution_6', color: '#80ff80', level: 11}
,{id: '11', label: 'Matched requiremets noted', type: 'event', group: 'Solution_6', color: '#FF8080', level: 12}
,{id: '28', label: 'Handing in at Managers office', type: 'function', group: 'Solution_6', color: '#80ff80', level: 13}
,{id: '13', label: 'xor', type: 'operator', group: 'Solution_6', color: 'gray', level: 14}
,{id: '19', label: 'Application rejected', type: 'event', group: 'Solution_6', color: '#FF8080', level: 15}
,{id: '20', label: 'xor', type: 'operator', group: 'Solution_6', color: 'gray', level: 16}
,{id: '18', label: 'Informing Employee', type: 'function', group: 'Solution_6', color: '#80ff80', level: 17}
,{id: '7', label: 'xor', type: 'operator', group: 'Solution_6', color: 'gray', level: 18}
,{id: '21', label: 'Business trip approved', type: 'event', group: 'Solution_6', color: '#FF8080', level: 19}
,{id: '26', label: 'Checking car availability', type: 'function', group: 'Solution_6', color: '#80ff80', level: 20}
,{id: '30', label: 'xor', type: 'operator', group: 'Solution_6', color: 'gray', level: 21}
,{id: '8', label: 'Car not available', type: 'event', group: 'Solution_6', color: '#FF8080', level: 22}
,{id: '32', label: 'Order Rental car', type: 'function', group: 'Solution_6', color: '#80ff80', level: 23}
,{id: '12', label: 'Rental car booked', type: 'event', group: 'Solution_6', color: '#FF8080', level: 24}
,{id: '40', label: 'xor', type: 'operator', group: 'Solution_6', color: 'gray', level: 25}
,{id: '42', label: 'Car available', type: 'event', group: 'Solution_6', color: '#FF8080', level: 22}
,{id: '37', label: 'Book the company car', type: 'function', group: 'Solution_6', color: '#80ff80', level: 23}
,{id: '24', label: 'Company car booked', type: 'event', group: 'Solution_6', color: '#FF8080', level: 24}
,{id: '23', label: 'Business trip not approved', type: 'event', group: 'Solution_6', color: '#FF8080', level: 19}
,{id: '41', label: 'xor', type: 'operator', group: 'Solution_6', color: 'gray', level: 20}
,{id: '16', label: 'Checking for discard or integration', type: 'function', group: 'Solution_6', color: '#80ff80', level: 21}
,{id: '34', label: 'Business trip discarted', type: 'event', group: 'Solution_6', color: '#FF8080', level: 22}
,{id: '33', label: 'Reviewing the business trip application', type: 'function', group: 'Solution_6', color: '#80ff80', level: 21}
,{id: '5', label: 'Application accepted', type: 'event', group: 'Solution_6', color: '#FF8080', level: 15}
,{id: '27', label: 'Making a note of employee and period of trip', type: 'function', group: 'Solution_6', color: '#80ff80', level: 16}
,{id: '1', label: 'Application registered', type: 'event', group: 'Solution_6', color: '#FF8080', level: 17}
,{id: '35', label: 'Application as per the requirements', type: 'event', group: 'Solution_6', color: '#FF8080', level: 9}
]; edges = [{from: '1', to: '20', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '2', to: '17', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '4', to: '14', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '5', to: '27', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '6', to: '22', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '7', to: '21', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '7', to: '23', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '8', to: '32', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '9', to: '43', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '10', to: '9', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '11', to: '28', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '12', to: '40', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '13', to: '19', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '13', to: '5', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '14', to: '29', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '15', to: '6', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '16', to: '34', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '17', to: '38', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '18', to: '7', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '19', to: '20', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '20', to: '18', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '21', to: '26', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '22', to: '25', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '23', to: '41', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '24', to: '40', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '25', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '26', to: '30', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '27', to: '1', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '28', to: '13', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '29', to: '31', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '30', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '30', to: '42', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '31', to: '10', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '31', to: '35', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '32', to: '12', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '33', to: '17', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '35', to: '9', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '36', to: '15', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '37', to: '24', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '38', to: '4', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '39', to: '2', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '40', to: '15', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '41', to: '16', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '41', to: '33', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '42', to: '37', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '43', to: '11', arrows:'to', style: 'arrow', color: 'gray'}
]; satelliteObjects = []; relations = [];
		
          var musterErgebnis = "";

          expect(setNodesAndEdges(undefined, nodes, edges, satelliteObjects, relations, 100, 50, 0, 0, true)).toEqual(musterErgebnis);
      });
	
	
	
      it("Exams Solution 6 [0, 10, 0, 0]", function () {
		
		  var  nodes = [{id: '36', label: 'Date of business trip reached', type: 'event', group: 'Solution_6', color: '#FF8080', level: 1}
,{id: '15', label: 'xor', type: 'operator', group: 'Solution_6', color: 'gray', level: 2}
,{id: '6', label: 'Realizing business trip', type: 'function', group: 'Solution_6', color: '#80ff80', level: 3}
,{id: '22', label: 'Business trip realized', type: 'event', group: 'Solution_6', color: '#FF8080', level: 4}
,{id: '25', label: 'Accounting for business trip takes place', type: 'function', group: 'Solution_6', color: '#80ff80', level: 5}
,{id: '3', label: 'Business trip accounted', type: 'event', group: 'Solution_6', color: '#FF8080', level: 6}
,{id: '39', label: 'Business Trip application need', type: 'event', group: 'Solution_6', color: '#FF8080', level: 1}
,{id: '2', label: 'Application filling', type: 'function', group: 'Solution_6', color: '#80ff80', level: 2}
,{id: '17', label: 'and', type: 'operator', group: 'Solution_6', color: 'gray', level: 3}
,{id: '38', label: 'Application filled', type: 'event', group: 'Solution_6', color: '#FF8080', level: 4}
,{id: '4', label: 'Handing in for approval', type: 'function', group: 'Solution_6', color: '#80ff80', level: 5}
,{id: '14', label: 'Application handed in', type: 'event', group: 'Solution_6', color: '#FF8080', level: 6}
,{id: '29', label: 'Application filling', type: 'function', group: 'Solution_6', color: '#80ff80', level: 7}
,{id: '31', label: 'xor', type: 'operator', group: 'Solution_6', color: 'gray', level: 8}
,{id: '10', label: 'Application as not per the requirements', type: 'event', group: 'Solution_6', color: '#FF8080', level: 9}
,{id: '9', label: 'xor', type: 'operator', group: 'Solution_6', color: 'gray', level: 10}
,{id: '43', label: 'Matching the requirements', type: 'function', group: 'Solution_6', color: '#80ff80', level: 11}
,{id: '11', label: 'Matched requiremets noted', type: 'event', group: 'Solution_6', color: '#FF8080', level: 12}
,{id: '28', label: 'Handing in at Managers office', type: 'function', group: 'Solution_6', color: '#80ff80', level: 13}
,{id: '13', label: 'xor', type: 'operator', group: 'Solution_6', color: 'gray', level: 14}
,{id: '19', label: 'Application rejected', type: 'event', group: 'Solution_6', color: '#FF8080', level: 15}
,{id: '20', label: 'xor', type: 'operator', group: 'Solution_6', color: 'gray', level: 16}
,{id: '18', label: 'Informing Employee', type: 'function', group: 'Solution_6', color: '#80ff80', level: 17}
,{id: '7', label: 'xor', type: 'operator', group: 'Solution_6', color: 'gray', level: 18}
,{id: '21', label: 'Business trip approved', type: 'event', group: 'Solution_6', color: '#FF8080', level: 19}
,{id: '26', label: 'Checking car availability', type: 'function', group: 'Solution_6', color: '#80ff80', level: 20}
,{id: '30', label: 'xor', type: 'operator', group: 'Solution_6', color: 'gray', level: 21}
,{id: '8', label: 'Car not available', type: 'event', group: 'Solution_6', color: '#FF8080', level: 22}
,{id: '32', label: 'Order Rental car', type: 'function', group: 'Solution_6', color: '#80ff80', level: 23}
,{id: '12', label: 'Rental car booked', type: 'event', group: 'Solution_6', color: '#FF8080', level: 24}
,{id: '40', label: 'xor', type: 'operator', group: 'Solution_6', color: 'gray', level: 25}
,{id: '42', label: 'Car available', type: 'event', group: 'Solution_6', color: '#FF8080', level: 22}
,{id: '37', label: 'Book the company car', type: 'function', group: 'Solution_6', color: '#80ff80', level: 23}
,{id: '24', label: 'Company car booked', type: 'event', group: 'Solution_6', color: '#FF8080', level: 24}
,{id: '23', label: 'Business trip not approved', type: 'event', group: 'Solution_6', color: '#FF8080', level: 19}
,{id: '41', label: 'xor', type: 'operator', group: 'Solution_6', color: 'gray', level: 20}
,{id: '16', label: 'Checking for discard or integration', type: 'function', group: 'Solution_6', color: '#80ff80', level: 21}
,{id: '34', label: 'Business trip discarted', type: 'event', group: 'Solution_6', color: '#FF8080', level: 22}
,{id: '33', label: 'Reviewing the business trip application', type: 'function', group: 'Solution_6', color: '#80ff80', level: 21}
,{id: '5', label: 'Application accepted', type: 'event', group: 'Solution_6', color: '#FF8080', level: 15}
,{id: '27', label: 'Making a note of employee and period of trip', type: 'function', group: 'Solution_6', color: '#80ff80', level: 16}
,{id: '1', label: 'Application registered', type: 'event', group: 'Solution_6', color: '#FF8080', level: 17}
,{id: '35', label: 'Application as per the requirements', type: 'event', group: 'Solution_6', color: '#FF8080', level: 9}
]; edges = [{from: '1', to: '20', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '2', to: '17', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '4', to: '14', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '5', to: '27', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '6', to: '22', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '7', to: '21', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '7', to: '23', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '8', to: '32', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '9', to: '43', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '10', to: '9', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '11', to: '28', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '12', to: '40', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '13', to: '19', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '13', to: '5', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '14', to: '29', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '15', to: '6', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '16', to: '34', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '17', to: '38', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '18', to: '7', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '19', to: '20', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '20', to: '18', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '21', to: '26', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '22', to: '25', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '23', to: '41', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '24', to: '40', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '25', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '26', to: '30', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '27', to: '1', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '28', to: '13', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '29', to: '31', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '30', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '30', to: '42', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '31', to: '10', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '31', to: '35', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '32', to: '12', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '33', to: '17', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '35', to: '9', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '36', to: '15', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '37', to: '24', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '38', to: '4', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '39', to: '2', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '40', to: '15', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '41', to: '16', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '41', to: '33', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '42', to: '37', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '43', to: '11', arrows:'to', style: 'arrow', color: 'gray'}
]; satelliteObjects = []; relations = [];
		
          var musterErgebnis = "";

          expect(setNodesAndEdges(undefined, nodes, edges, satelliteObjects, relations, 0, 10, 0, 0, true)).toEqual(musterErgebnis);
      });
	  
	        it("Exams Solution 7 [100, 50, 0, 0]", function () {
		
		  var nodes = [{id: '26', label: 'Business travel application required', type: 'event', group: 'Solution_7', color: '#FF8080', level: 1}
,{id: '38', label: 'Fill Business trip application', type: 'function', group: 'Solution_7', color: '#80ff80', level: 2}
,{id: '29', label: 'or', type: 'operator', group: 'Solution_7', color: 'gray', level: 3}
,{id: '19', label: 'Business trip application completed', type: 'event', group: 'Solution_7', color: '#FF8080', level: 4}
,{id: '8', label: 'Handling in the Business trip', type: 'function', group: 'Solution_7', color: '#80ff80', level: 5}
,{id: '27', label: 'Business trip application handled in', type: 'event', group: 'Solution_7', color: '#FF8080', level: 6}
,{id: '15', label: 'Reviewing business trip application', type: 'function', group: 'Solution_7', color: '#80ff80', level: 7}
,{id: '39', label: 'xor', type: 'operator', group: 'Solution_7', color: 'gray', level: 8}
,{id: '3', label: 'Business trip application accordance with requirements', type: 'event', group: 'Solution_7', color: '#FF8080', level: 9}
,{id: '40', label: 'xor', type: 'operator', group: 'Solution_7', color: 'gray', level: 10}
,{id: '30', label: 'No matches with the requirements', type: 'function', group: 'Solution_7', color: '#80ff80', level: 11}
,{id: '9', label: 'Matches noted', type: 'event', group: 'Solution_7', color: '#FF8080', level: 12}
,{id: '17', label: 'Handling at the managers office', type: 'function', group: 'Solution_7', color: '#80ff80', level: 13}
,{id: '12', label: 'xor', type: 'operator', group: 'Solution_7', color: 'gray', level: 14}
,{id: '14', label: 'Business trip application is rejected', type: 'event', group: 'Solution_7', color: '#FF8080', level: 15}
,{id: '1', label: 'xor', type: 'operator', group: 'Solution_7', color: 'gray', level: 16}
,{id: '2', label: 'Informing employee', type: 'function', group: 'Solution_7', color: '#80ff80', level: 17}
,{id: '20', label: 'xor', type: 'operator', group: 'Solution_7', color: 'gray', level: 18}
,{id: '33', label: 'Business trip approved', type: 'event', group: 'Solution_7', color: '#FF8080', level: 19}
,{id: '7', label: 'Checking availability of company car', type: 'function', group: 'Solution_7', color: '#80ff80', level: 20}
,{id: '34', label: 'xor', type: 'operator', group: 'Solution_7', color: 'gray', level: 21}
,{id: '35', label: 'Company car not available', type: 'event', group: 'Solution_7', color: '#FF8080', level: 22}
,{id: '4', label: 'Booking rental car', type: 'function', group: 'Solution_7', color: '#80ff80', level: 23}
,{id: '10', label: 'Rental car booked', type: 'event', group: 'Solution_7', color: '#FF8080', level: 24}
,{id: '25', label: 'xor', type: 'operator', group: 'Solution_7', color: 'gray', level: 25}
,{id: '16', label: 'Business trip realized', type: 'event', group: 'Solution_7', color: '#FF8080', level: 26}
,{id: '41', label: 'Accounting for business trip', type: 'function', group: 'Solution_7', color: '#80ff80', level: 27}
,{id: '13', label: 'Business trip accounted', type: 'event', group: 'Solution_7', color: '#FF8080', level: 28}
,{id: '6', label: 'Company car available', type: 'event', group: 'Solution_7', color: '#FF8080', level: 22}
,{id: '21', label: 'Booking company car', type: 'function', group: 'Solution_7', color: '#80ff80', level: 23}
,{id: '36', label: 'Company car booked', type: 'event', group: 'Solution_7', color: '#FF8080', level: 24}
,{id: '22', label: 'Business trip rejected', type: 'event', group: 'Solution_7', color: '#FF8080', level: 19}
,{id: '24', label: 'Reviewing decision', type: 'function', group: 'Solution_7', color: '#80ff80', level: 20}
,{id: '31', label: 'xor', type: 'operator', group: 'Solution_7', color: 'gray', level: 21}
,{id: '28', label: 'Business trip not rejected', type: 'event', group: 'Solution_7', color: '#FF8080', level: 22}
,{id: '11', label: 'Reviewing business trip application', type: 'function', group: 'Solution_7', color: '#80ff80', level: 23}
,{id: '23', label: 'Business trip still rejected', type: 'event', group: 'Solution_7', color: '#FF8080', level: 22}
,{id: '32', label: 'Business trip application is approved', type: 'event', group: 'Solution_7', color: '#FF8080', level: 15}
,{id: '5', label: 'Entering the name and the trip period', type: 'function', group: 'Solution_7', color: '#80ff80', level: 16}
,{id: '37', label: 'Business trip registered', type: 'event', group: 'Solution_7', color: '#FF8080', level: 17}
,{id: '18', label: 'Business trip application not in accordance with requirements', type: 'event', group: 'Solution_7', color: '#FF8080', level: 9}
]; edges = [{from: '1', to: '2', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '2', to: '20', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '3', to: '40', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '4', to: '10', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '5', to: '37', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '6', to: '21', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '7', to: '34', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '8', to: '27', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '9', to: '17', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '10', to: '25', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '11', to: '29', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '12', to: '14', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '12', to: '32', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '14', to: '1', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '15', to: '39', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '16', to: '41', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '17', to: '12', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '18', to: '40', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '19', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '20', to: '33', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '20', to: '22', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '21', to: '36', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '22', to: '24', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '24', to: '31', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '25', to: '16', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '26', to: '38', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '27', to: '15', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '28', to: '11', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '29', to: '19', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '30', to: '9', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '31', to: '28', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '31', to: '23', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '32', to: '5', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '33', to: '7', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '34', to: '35', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '34', to: '6', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '35', to: '4', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '36', to: '25', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '37', to: '1', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '38', to: '29', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '39', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '39', to: '18', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '40', to: '30', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '41', to: '13', arrows:'to', style: 'arrow', color: 'gray'}
]; satelliteObjects = []; relations = [];
		
          var musterErgebnis = "";

          expect(setNodesAndEdges(undefined, nodes, edges, satelliteObjects, relations, 100, 50, 0, 0, true)).toEqual(musterErgebnis);
      });
	
	
	
      it("Exams Solution 7 [0, 10, 0, 0]", function () {
		
		  var  nodes = [{id: '26', label: 'Business travel application required', type: 'event', group: 'Solution_7', color: '#FF8080', level: 1}
,{id: '38', label: 'Fill Business trip application', type: 'function', group: 'Solution_7', color: '#80ff80', level: 2}
,{id: '29', label: 'or', type: 'operator', group: 'Solution_7', color: 'gray', level: 3}
,{id: '19', label: 'Business trip application completed', type: 'event', group: 'Solution_7', color: '#FF8080', level: 4}
,{id: '8', label: 'Handling in the Business trip', type: 'function', group: 'Solution_7', color: '#80ff80', level: 5}
,{id: '27', label: 'Business trip application handled in', type: 'event', group: 'Solution_7', color: '#FF8080', level: 6}
,{id: '15', label: 'Reviewing business trip application', type: 'function', group: 'Solution_7', color: '#80ff80', level: 7}
,{id: '39', label: 'xor', type: 'operator', group: 'Solution_7', color: 'gray', level: 8}
,{id: '3', label: 'Business trip application accordance with requirements', type: 'event', group: 'Solution_7', color: '#FF8080', level: 9}
,{id: '40', label: 'xor', type: 'operator', group: 'Solution_7', color: 'gray', level: 10}
,{id: '30', label: 'No matches with the requirements', type: 'function', group: 'Solution_7', color: '#80ff80', level: 11}
,{id: '9', label: 'Matches noted', type: 'event', group: 'Solution_7', color: '#FF8080', level: 12}
,{id: '17', label: 'Handling at the managers office', type: 'function', group: 'Solution_7', color: '#80ff80', level: 13}
,{id: '12', label: 'xor', type: 'operator', group: 'Solution_7', color: 'gray', level: 14}
,{id: '14', label: 'Business trip application is rejected', type: 'event', group: 'Solution_7', color: '#FF8080', level: 15}
,{id: '1', label: 'xor', type: 'operator', group: 'Solution_7', color: 'gray', level: 16}
,{id: '2', label: 'Informing employee', type: 'function', group: 'Solution_7', color: '#80ff80', level: 17}
,{id: '20', label: 'xor', type: 'operator', group: 'Solution_7', color: 'gray', level: 18}
,{id: '33', label: 'Business trip approved', type: 'event', group: 'Solution_7', color: '#FF8080', level: 19}
,{id: '7', label: 'Checking availability of company car', type: 'function', group: 'Solution_7', color: '#80ff80', level: 20}
,{id: '34', label: 'xor', type: 'operator', group: 'Solution_7', color: 'gray', level: 21}
,{id: '35', label: 'Company car not available', type: 'event', group: 'Solution_7', color: '#FF8080', level: 22}
,{id: '4', label: 'Booking rental car', type: 'function', group: 'Solution_7', color: '#80ff80', level: 23}
,{id: '10', label: 'Rental car booked', type: 'event', group: 'Solution_7', color: '#FF8080', level: 24}
,{id: '25', label: 'xor', type: 'operator', group: 'Solution_7', color: 'gray', level: 25}
,{id: '16', label: 'Business trip realized', type: 'event', group: 'Solution_7', color: '#FF8080', level: 26}
,{id: '41', label: 'Accounting for business trip', type: 'function', group: 'Solution_7', color: '#80ff80', level: 27}
,{id: '13', label: 'Business trip accounted', type: 'event', group: 'Solution_7', color: '#FF8080', level: 28}
,{id: '6', label: 'Company car available', type: 'event', group: 'Solution_7', color: '#FF8080', level: 22}
,{id: '21', label: 'Booking company car', type: 'function', group: 'Solution_7', color: '#80ff80', level: 23}
,{id: '36', label: 'Company car booked', type: 'event', group: 'Solution_7', color: '#FF8080', level: 24}
,{id: '22', label: 'Business trip rejected', type: 'event', group: 'Solution_7', color: '#FF8080', level: 19}
,{id: '24', label: 'Reviewing decision', type: 'function', group: 'Solution_7', color: '#80ff80', level: 20}
,{id: '31', label: 'xor', type: 'operator', group: 'Solution_7', color: 'gray', level: 21}
,{id: '28', label: 'Business trip not rejected', type: 'event', group: 'Solution_7', color: '#FF8080', level: 22}
,{id: '11', label: 'Reviewing business trip application', type: 'function', group: 'Solution_7', color: '#80ff80', level: 23}
,{id: '23', label: 'Business trip still rejected', type: 'event', group: 'Solution_7', color: '#FF8080', level: 22}
,{id: '32', label: 'Business trip application is approved', type: 'event', group: 'Solution_7', color: '#FF8080', level: 15}
,{id: '5', label: 'Entering the name and the trip period', type: 'function', group: 'Solution_7', color: '#80ff80', level: 16}
,{id: '37', label: 'Business trip registered', type: 'event', group: 'Solution_7', color: '#FF8080', level: 17}
,{id: '18', label: 'Business trip application not in accordance with requirements', type: 'event', group: 'Solution_7', color: '#FF8080', level: 9}
]; edges = [{from: '1', to: '2', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '2', to: '20', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '3', to: '40', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '4', to: '10', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '5', to: '37', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '6', to: '21', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '7', to: '34', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '8', to: '27', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '9', to: '17', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '10', to: '25', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '11', to: '29', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '12', to: '14', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '12', to: '32', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '14', to: '1', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '15', to: '39', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '16', to: '41', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '17', to: '12', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '18', to: '40', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '19', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '20', to: '33', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '20', to: '22', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '21', to: '36', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '22', to: '24', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '24', to: '31', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '25', to: '16', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '26', to: '38', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '27', to: '15', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '28', to: '11', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '29', to: '19', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '30', to: '9', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '31', to: '28', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '31', to: '23', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '32', to: '5', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '33', to: '7', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '34', to: '35', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '34', to: '6', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '35', to: '4', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '36', to: '25', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '37', to: '1', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '38', to: '29', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '39', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '39', to: '18', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '40', to: '30', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '41', to: '13', arrows:'to', style: 'arrow', color: 'gray'}
]; satelliteObjects = []; relations = [];
		
          var musterErgebnis = "";

          expect(setNodesAndEdges(undefined, nodes, edges, satelliteObjects, relations, 0, 10, 0, 0, true)).toEqual(musterErgebnis);
      });

});