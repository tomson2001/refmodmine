describe( "synthetic test models", function () {



      it("simple sequence [100, 50, 0, 0]", function () {
		
		  var nodes2 = [{id: 'dce6c11b-25e2-4c91-81f2-ae0b961ceebf', label: 'F1', type: 'function', group: 'simple Sequence', color: '#80ff80', level: 1}
  ,{id: '052b2ef0-2840-4852-a49b-8ae134b2e25a', label: 'E1', type: 'event', group: 'simple Sequence', color: '#FF8080', level: 2}
  ,{id: '19ea199c-a60b-4a17-acfc-e0a2c1874a88', label: 'F2', type: 'function', group: 'simple Sequence', color: '#80ff80', level: 3}
  ];
  var edges2 = [{from: '052b2ef0-2840-4852-a49b-8ae134b2e25a', to: '19ea199c-a60b-4a17-acfc-e0a2c1874a88', arrows:'to', style: 'arrow', color: 'gray'}
  ,{from: 'dce6c11b-25e2-4c91-81f2-ae0b961ceebf', to: '052b2ef0-2840-4852-a49b-8ae134b2e25a', arrows:'to', style: 'arrow', color: 'gray'}
  ];
  var satelliteObjects2 = [];
  var relations2 = [];
		
          var musterErgebnis2 = "node F1: (0, 0)node E1: (0, 100)node F2: (0, 200)E1 -> F2: [(0, 125)(0, 175)]F1 -> E1: [(0, 25)(0, 75)]";

          expect(setNodesAndEdges("test", undefined, nodes2, edges2, satelliteObjects2, relations2, 100, 50, 0, 0, true)).toEqual(musterErgebnis2);
      });
	
	
	
	          it("simple sequence [0, 10, 0, 0]", function () {
		
		  var nodes2 = [{id: 'dce6c11b-25e2-4c91-81f2-ae0b961ceebf', label: 'F1', type: 'function', group: 'simple Sequence', color: '#80ff80', level: 1}
  ,{id: '052b2ef0-2840-4852-a49b-8ae134b2e25a', label: 'E1', type: 'event', group: 'simple Sequence', color: '#FF8080', level: 2}
  ,{id: '19ea199c-a60b-4a17-acfc-e0a2c1874a88', label: 'F2', type: 'function', group: 'simple Sequence', color: '#80ff80', level: 3}
  ];
  var edges2 = [{from: '052b2ef0-2840-4852-a49b-8ae134b2e25a', to: '19ea199c-a60b-4a17-acfc-e0a2c1874a88', arrows:'to', style: 'arrow', color: 'gray'}
  ,{from: 'dce6c11b-25e2-4c91-81f2-ae0b961ceebf', to: '052b2ef0-2840-4852-a49b-8ae134b2e25a', arrows:'to', style: 'arrow', color: 'gray'}
  ];
  var satelliteObjects2 = [];
  var relations2 = [];
		
          var musterErgebnis2 = "node F1: (0, 0)node E1: (0, 60)node F2: (0, 120)E1 -> F2: [(0, 85)(0, 95)]F1 -> E1: [(0, 25)(0, 35)]";

          expect(setNodesAndEdges("test", undefined, nodes2, edges2, satelliteObjects2, relations2, 0, 10, 0, 0, true)).toEqual(musterErgebnis2);
      });
	
	
	
	
	      it("simple Loop [100, 50, 0, 0]", function () {
		
  var nodes = [{id: 'ea0bc58a-2264-491d-88a2-1b1ac0f4156a', label: 'F_1', type: 'function', group: 'EPC4_SimpleLoop', color: '#80ff80', level: 1}
  ,{id: '65e161a4-4733-4894-be2e-34155fe16c71', label: 'xor', type: 'operator', group: 'EPC4_SimpleLoop', color: 'gray', level: 2}
  ,{id: 'f9cb515f-f4a0-4c02-a1db-215bbf785250', label: 'F_3', type: 'function', group: 'EPC4_SimpleLoop', color: '#80ff80', level: 3}
  ,{id: 'b7d32a7c-a1ad-4418-a7f3-c292065efe2d', label: 'xor', type: 'operator', group: 'EPC4_SimpleLoop', color: 'gray', level: 4}
  ,{id: '26c5e942-1f2e-4a91-b841-4f9bb8022b55', label: 'F_4', type: 'function', group: 'EPC4_SimpleLoop', color: '#80ff80', level: 5}
  ]; 
  var edges = [{from: 'ea0bc58a-2264-491d-88a2-1b1ac0f4156a', to: '65e161a4-4733-4894-be2e-34155fe16c71', arrows:'to', style: 'arrow', color: 'gray'}
  ,{from: 'b7d32a7c-a1ad-4418-a7f3-c292065efe2d', to: '26c5e942-1f2e-4a91-b841-4f9bb8022b55', arrows:'to', style: 'arrow', color: 'gray'}
  ,{from: 'b7d32a7c-a1ad-4418-a7f3-c292065efe2d', to: '65e161a4-4733-4894-be2e-34155fe16c71', arrows:'to', style: 'arrow', color: 'gray'}
  ,{from: 'f9cb515f-f4a0-4c02-a1db-215bbf785250', to: 'b7d32a7c-a1ad-4418-a7f3-c292065efe2d', arrows:'to', style: 'arrow', color: 'gray'}
  ,{from: '65e161a4-4733-4894-be2e-34155fe16c71', to: 'f9cb515f-f4a0-4c02-a1db-215bbf785250', arrows:'to', style: 'arrow', color: 'gray'}
  ];
  var satelliteObjects = [];
  var relations = [];


  var musterErgebnis = "node F_1: (0, 0)node xor: (0, 100)node F_3: (0, 200)node xor: (0, 300)node F_4: (0, 400)F_1 -> xor: [(0, 25)(0, 75)]xor -> F_4: [(0, 325)(0, 375)]F_3 -> xor: [(0, 225)(0, 275)]xor -> F_3: [(0, 125)(0, 175)]xor -> xor: [(25, 300)(150, 300)(150, 100)(25, 100)]";



          expect(setNodesAndEdges("test", undefined, nodes, edges, satelliteObjects, relations, 100, 50, 0, 0, true)).toEqual(musterErgebnis);
      });
	
	
		      it("simple Loop [0, 10, 0, 0]", function () {
		
  var nodes = [{id: 'ea0bc58a-2264-491d-88a2-1b1ac0f4156a', label: 'F_1', type: 'function', group: 'EPC4_SimpleLoop', color: '#80ff80', level: 1}
  ,{id: '65e161a4-4733-4894-be2e-34155fe16c71', label: 'xor', type: 'operator', group: 'EPC4_SimpleLoop', color: 'gray', level: 2}
  ,{id: 'f9cb515f-f4a0-4c02-a1db-215bbf785250', label: 'F_3', type: 'function', group: 'EPC4_SimpleLoop', color: '#80ff80', level: 3}
  ,{id: 'b7d32a7c-a1ad-4418-a7f3-c292065efe2d', label: 'xor', type: 'operator', group: 'EPC4_SimpleLoop', color: 'gray', level: 4}
  ,{id: '26c5e942-1f2e-4a91-b841-4f9bb8022b55', label: 'F_4', type: 'function', group: 'EPC4_SimpleLoop', color: '#80ff80', level: 5}
  ]; 
  var edges = [{from: 'ea0bc58a-2264-491d-88a2-1b1ac0f4156a', to: '65e161a4-4733-4894-be2e-34155fe16c71', arrows:'to', style: 'arrow', color: 'gray'}
  ,{from: 'b7d32a7c-a1ad-4418-a7f3-c292065efe2d', to: '26c5e942-1f2e-4a91-b841-4f9bb8022b55', arrows:'to', style: 'arrow', color: 'gray'}
  ,{from: 'b7d32a7c-a1ad-4418-a7f3-c292065efe2d', to: '65e161a4-4733-4894-be2e-34155fe16c71', arrows:'to', style: 'arrow', color: 'gray'}
  ,{from: 'f9cb515f-f4a0-4c02-a1db-215bbf785250', to: 'b7d32a7c-a1ad-4418-a7f3-c292065efe2d', arrows:'to', style: 'arrow', color: 'gray'}
  ,{from: '65e161a4-4733-4894-be2e-34155fe16c71', to: 'f9cb515f-f4a0-4c02-a1db-215bbf785250', arrows:'to', style: 'arrow', color: 'gray'}
  ];
  var satelliteObjects = [];
  var relations = [];


  var musterErgebnis = "node F_1: (0, 0)node xor: (0, 60)node F_3: (0, 120)node xor: (0, 180)node F_4: (0, 240)F_1 -> xor: [(0, 25)(0, 35)]xor -> F_4: [(0, 205)(0, 215)]F_3 -> xor: [(0, 145)(0, 155)]xor -> F_3: [(0, 85)(0, 95)]xor -> xor: [(25, 180)(50, 180)(50, 60)(25, 60)]";



          expect(setNodesAndEdges("test", undefined, nodes, edges, satelliteObjects, relations, 0, 10, 0, 0, true)).toEqual(musterErgebnis);
      });
	 
	 			  it("simple Connector [100, 50, 0, 0]", function () {
		
		  var nodes = [{id: 'c138e641-8e62-416d-8a3a-0cf6bfebc12d', label: 'F1', type: 'function', group: 'simple_Connector', color: '#80ff80', level: 1}
  ,{id: 'eec8fcd3-3699-4b26-b28e-f5c307c3ecc7', label: 'xor', type: 'operator', group: 'simple_Connector', color: 'gray', level: 2}
  ,{id: '8ac80ca2-cf7d-434a-a3eb-b616682201c9', label: 'E1', type: 'event', group: 'simple_Connector', color: '#FF8080', level: 3}
  ]; edges = [{from: 'c138e641-8e62-416d-8a3a-0cf6bfebc12d', to: 'eec8fcd3-3699-4b26-b28e-f5c307c3ecc7', arrows:'to', style: 'arrow', color: 'gray'}
  ,{from: 'eec8fcd3-3699-4b26-b28e-f5c307c3ecc7', to: '8ac80ca2-cf7d-434a-a3eb-b616682201c9', arrows:'to', style: 'arrow', color: 'gray'}];
		  var satelliteObjects = [];
		  var relations = [];


		  var musterErgebnis = "node F1: (0, 0)node xor: (0, 100)node E1: (0, 200)F1 -> xor: [(0, 25)(0, 75)]xor -> E1: [(0, 125)(0, 175)]";



          expect(setNodesAndEdges("test", undefined, nodes, edges, satelliteObjects, relations, 100, 50, 0, 0, true)).toEqual(musterErgebnis);
      });
	
	  it("simple Connector [0, 10, 0, 0]", function () {
		
		  var nodes = [{id: 'c138e641-8e62-416d-8a3a-0cf6bfebc12d', label: 'F1', type: 'function', group: 'simple_Connector', color: '#80ff80', level: 1}
  ,{id: 'eec8fcd3-3699-4b26-b28e-f5c307c3ecc7', label: 'xor', type: 'operator', group: 'simple_Connector', color: 'gray', level: 2}
  ,{id: '8ac80ca2-cf7d-434a-a3eb-b616682201c9', label: 'E1', type: 'event', group: 'simple_Connector', color: '#FF8080', level: 3}
  ]; edges = [{from: 'c138e641-8e62-416d-8a3a-0cf6bfebc12d', to: 'eec8fcd3-3699-4b26-b28e-f5c307c3ecc7', arrows:'to', style: 'arrow', color: 'gray'}
  ,{from: 'eec8fcd3-3699-4b26-b28e-f5c307c3ecc7', to: '8ac80ca2-cf7d-434a-a3eb-b616682201c9', arrows:'to', style: 'arrow', color: 'gray'}];
		  var satelliteObjects = [];
		  var relations = [];


		  var musterErgebnis = "node F1: (0, 0)node xor: (0, 60)node E1: (0, 120)F1 -> xor: [(0, 25)(0, 35)]xor -> E1: [(0, 85)(0, 95)]";



          expect(setNodesAndEdges("test", undefined, nodes, edges, satelliteObjects, relations, 0, 10, 0, 0, true)).toEqual(musterErgebnis);
      });
	
	  it("simple Join 2 elements [100, 50, 0, 0]", function () {
		
		  var nodes = [{id: '72829ec4-d484-4d19-b042-1303152b64a1', label: 'F1', type: 'function', group: 'simple_Join_2_elements', color: '#80ff80', level: 1}
  ,{id: '8fe41947-5172-4699-9710-90e9d5aac42e', label: 'xor', type: 'operator', group: 'simple_Join_2_elements', color: 'gray', level: 2}
  ,{id: '880ce617-db65-492c-bdcb-e3eeaa6a576f', label: 'E1', type: 'event', group: 'simple_Join_2_elements', color: '#FF8080', level: 3}
  ,{id: '11730ce4-10c2-4058-9c67-268607562e81', label: 'F2', type: 'function', group: 'simple_Join_2_elements', color: '#80ff80', level: 1}
  ]; edges = [{from: '72829ec4-d484-4d19-b042-1303152b64a1', to: '8fe41947-5172-4699-9710-90e9d5aac42e', arrows:'to', style: 'arrow', color: 'gray'}
  ,{from: '11730ce4-10c2-4058-9c67-268607562e81', to: '8fe41947-5172-4699-9710-90e9d5aac42e', arrows:'to', style: 'arrow', color: 'gray'}
  ,{from: '8fe41947-5172-4699-9710-90e9d5aac42e', to: '880ce617-db65-492c-bdcb-e3eeaa6a576f', arrows:'to', style: 'arrow', color: 'gray'}
  ];
		  var satelliteObjects = [];
		  var relations = [];


		  var musterErgebnis = "node xor: (0, 0)node E1: (0, 100)node F2: (100, -100)node F1: (-100, -100)F1 -> xor: [(-100, -75)(-100, 0)(-25, 0)]F2 -> xor: [(100, -75)(100, 0)(25, 0)]xor -> E1: [(0, 25)(0, 75)]";



          expect(setNodesAndEdges("test", undefined, nodes, edges, satelliteObjects, relations, 100, 50, 0, 0, true)).toEqual(musterErgebnis);
      });
	
	  it("simple Join 2 elements [0, 10, 0, 0]", function () {
		
		  var nodes = [{id: '72829ec4-d484-4d19-b042-1303152b64a1', label: 'F1', type: 'function', group: 'simple_Join_2_elements', color: '#80ff80', level: 1}
  ,{id: '8fe41947-5172-4699-9710-90e9d5aac42e', label: 'xor', type: 'operator', group: 'simple_Join_2_elements', color: 'gray', level: 2}
  ,{id: '880ce617-db65-492c-bdcb-e3eeaa6a576f', label: 'E1', type: 'event', group: 'simple_Join_2_elements', color: '#FF8080', level: 3}
  ,{id: '11730ce4-10c2-4058-9c67-268607562e81', label: 'F2', type: 'function', group: 'simple_Join_2_elements', color: '#80ff80', level: 1}
  ]; edges = [{from: '72829ec4-d484-4d19-b042-1303152b64a1', to: '8fe41947-5172-4699-9710-90e9d5aac42e', arrows:'to', style: 'arrow', color: 'gray'}
  ,{from: '11730ce4-10c2-4058-9c67-268607562e81', to: '8fe41947-5172-4699-9710-90e9d5aac42e', arrows:'to', style: 'arrow', color: 'gray'}
  ,{from: '8fe41947-5172-4699-9710-90e9d5aac42e', to: '880ce617-db65-492c-bdcb-e3eeaa6a576f', arrows:'to', style: 'arrow', color: 'gray'}
  ];
		  var satelliteObjects = [];
		  var relations = [];


		  var musterErgebnis = "node xor: (0, 0)node E1: (0, 60)node F2: (50, -60)node F1: (-50, -60)F1 -> xor: [(-50, -35)(-50, 0)(-25, 0)]F2 -> xor: [(50, -35)(50, 0)(25, 0)]xor -> E1: [(0, 25)(0, 35)]";



          expect(setNodesAndEdges("test", undefined, nodes, edges, satelliteObjects, relations, 0, 10, 0, 0, true)).toEqual(musterErgebnis);
      });
	
		  it("simple Join 3 elements [100, 50, 0, 0]", function () {
		
		  var nodes = [{id: '5d3b4a48-0ee7-465b-92e3-a8177c1411a5', label: 'F2', type: 'function', group: 'simple_Join_3_elements', color: '#80ff80', level: 1}
  ,{id: '55ce086f-cc8b-4e0d-8adf-a4cd8259e7ee', label: 'xor', type: 'operator', group: 'simple_Join_3_elements', color: 'gray', level: 2}
  ,{id: '67ec1ff9-6d6f-403c-8040-cf615e3a4ec2', label: 'E1', type: 'event', group: 'simple_Join_3_elements', color: '#FF8080', level: 3}
  ,{id: '90b65d46-6c49-4cf7-8c41-4a74c9dfcc66', label: 'F1', type: 'function', group: 'simple_Join_3_elements', color: '#80ff80', level: 1}
  ,{id: 'efe98179-a0e5-46d1-baeb-6b0708e32373', label: 'F3', type: 'function', group: 'simple_Join_3_elements', color: '#80ff80', level: 1}
  ]; edges = [{from: '5d3b4a48-0ee7-465b-92e3-a8177c1411a5', to: '55ce086f-cc8b-4e0d-8adf-a4cd8259e7ee', arrows:'to', style: 'arrow', color: 'gray'}
  ,{from: '90b65d46-6c49-4cf7-8c41-4a74c9dfcc66', to: '55ce086f-cc8b-4e0d-8adf-a4cd8259e7ee', arrows:'to', style: 'arrow', color: 'gray'}
  ,{from: 'efe98179-a0e5-46d1-baeb-6b0708e32373', to: '55ce086f-cc8b-4e0d-8adf-a4cd8259e7ee', arrows:'to', style: 'arrow', color: 'gray'}
  ,{from: '55ce086f-cc8b-4e0d-8adf-a4cd8259e7ee', to: '67ec1ff9-6d6f-403c-8040-cf615e3a4ec2', arrows:'to', style: 'arrow', color: 'gray'}
  ]; 
		  var satelliteObjects = [];
		  var relations = [];


		  var musterErgebnis = "node xor: (0, 0)node E1: (0, 100)node F1: (0, -100)node F2: (-200, -100)node F3: (200, -100)F2 -> xor: [(-200, -75)(-200, 0)(-25, 0)]F1 -> xor: [(0, -75)(0, -25)]F3 -> xor: [(200, -75)(200, 0)(25, 0)]xor -> E1: [(0, 25)(0, 75)]";



          expect(setNodesAndEdges("test", undefined, nodes, edges, satelliteObjects, relations, 100, 50, 0, 0, true)).toEqual(musterErgebnis);
      });
	
	  it("simple Join 3 elements [0, 10, 0, 0]", function () {
		
		  var nodes = [{id: '5d3b4a48-0ee7-465b-92e3-a8177c1411a5', label: 'F2', type: 'function', group: 'simple_Join_3_elements', color: '#80ff80', level: 1}
  ,{id: '55ce086f-cc8b-4e0d-8adf-a4cd8259e7ee', label: 'xor', type: 'operator', group: 'simple_Join_3_elements', color: 'gray', level: 2}
  ,{id: '67ec1ff9-6d6f-403c-8040-cf615e3a4ec2', label: 'E1', type: 'event', group: 'simple_Join_3_elements', color: '#FF8080', level: 3}
  ,{id: '90b65d46-6c49-4cf7-8c41-4a74c9dfcc66', label: 'F1', type: 'function', group: 'simple_Join_3_elements', color: '#80ff80', level: 1}
  ,{id: 'efe98179-a0e5-46d1-baeb-6b0708e32373', label: 'F3', type: 'function', group: 'simple_Join_3_elements', color: '#80ff80', level: 1}
  ]; edges = [{from: '5d3b4a48-0ee7-465b-92e3-a8177c1411a5', to: '55ce086f-cc8b-4e0d-8adf-a4cd8259e7ee', arrows:'to', style: 'arrow', color: 'gray'}
  ,{from: '90b65d46-6c49-4cf7-8c41-4a74c9dfcc66', to: '55ce086f-cc8b-4e0d-8adf-a4cd8259e7ee', arrows:'to', style: 'arrow', color: 'gray'}
  ,{from: 'efe98179-a0e5-46d1-baeb-6b0708e32373', to: '55ce086f-cc8b-4e0d-8adf-a4cd8259e7ee', arrows:'to', style: 'arrow', color: 'gray'}
  ,{from: '55ce086f-cc8b-4e0d-8adf-a4cd8259e7ee', to: '67ec1ff9-6d6f-403c-8040-cf615e3a4ec2', arrows:'to', style: 'arrow', color: 'gray'}
  ]; 
		  var satelliteObjects = [];
		  var relations = [];


		  var musterErgebnis = "node xor: (0, 0)node E1: (0, 60)node F1: (0, -60)node F2: (-100, -60)node F3: (100, -60)F2 -> xor: [(-100, -35)(-100, 0)(-25, 0)]F1 -> xor: [(0, -35)(0, -25)]F3 -> xor: [(100, -35)(100, 0)(25, 0)]xor -> E1: [(0, 25)(0, 35)]";



          expect(setNodesAndEdges("test", undefined, nodes, edges, satelliteObjects, relations, 0, 10, 0, 0, true)).toEqual(musterErgebnis);
      });
	
		  it("simple Join 4 elements [100, 50, 0, 0]", function () {
		
		  var nodes = [{id: 'c2a6ff93-2222-4345-a244-5dd7766ed619', label: 'F3', type: 'function', group: 'simple_Join_4_elements', color: '#80ff80', level: 1}
  ,{id: 'cada1271-5ed0-478b-98eb-15a52bb95a8b', label: 'xor', type: 'operator', group: 'simple_Join_4_elements', color: 'gray', level: 2}
  ,{id: 'd5f40c60-cf9f-492a-ab4e-2a9fc4d97b7d', label: 'E1', type: 'event', group: 'simple_Join_4_elements', color: '#FF8080', level: 3}
  ,{id: 'ae0b435a-2f3c-4b10-89f8-2726881094e7', label: 'F4', type: 'function', group: 'simple_Join_4_elements', color: '#80ff80', level: 1}
  ,{id: 'de721188-9c8e-4e42-8d8e-4db771949ef3', label: 'F2', type: 'function', group: 'simple_Join_4_elements', color: '#80ff80', level: 1}
  ,{id: '7ffefc47-d97a-4fe6-8ba7-fb5dfce2208a', label: 'F1', type: 'function', group: 'simple_Join_4_elements', color: '#80ff80', level: 1}
  ]; edges = [{from: 'c2a6ff93-2222-4345-a244-5dd7766ed619', to: 'cada1271-5ed0-478b-98eb-15a52bb95a8b', arrows:'to', style: 'arrow', color: 'gray'}
  ,{from: 'cada1271-5ed0-478b-98eb-15a52bb95a8b', to: 'd5f40c60-cf9f-492a-ab4e-2a9fc4d97b7d', arrows:'to', style: 'arrow', color: 'gray'}
  ,{from: 'ae0b435a-2f3c-4b10-89f8-2726881094e7', to: 'cada1271-5ed0-478b-98eb-15a52bb95a8b', arrows:'to', style: 'arrow', color: 'gray'}
  ,{from: 'de721188-9c8e-4e42-8d8e-4db771949ef3', to: 'cada1271-5ed0-478b-98eb-15a52bb95a8b', arrows:'to', style: 'arrow', color: 'gray'}
  ,{from: '7ffefc47-d97a-4fe6-8ba7-fb5dfce2208a', to: 'cada1271-5ed0-478b-98eb-15a52bb95a8b', arrows:'to', style: 'arrow', color: 'gray'}
  ];
		  var satelliteObjects = [];
		  var relations = [];


		  var musterErgebnis = "node xor: (0, 0)node E1: (0, 100)node F2: (100, -100)node F4: (-100, -100)node F3: (-300, -100)node F1: (300, -100)F3 -> xor: [(-300, -75)(-300, 0)(-25, 0)]xor -> E1: [(0, 25)(0, 75)]F4 -> xor: [(-100, -75)(-100, 0)(-25, 0)]F2 -> xor: [(100, -75)(100, 0)(25, 0)]F1 -> xor: [(300, -75)(300, 0)(25, 0)]";



          expect(setNodesAndEdges("test", undefined, nodes, edges, satelliteObjects, relations, 100, 50, 0, 0, true)).toEqual(musterErgebnis);
      });
	
	  it("simple Join 4 elements [0, 10, 0, 0]", function () {
		
		  var nodes = [{id: 'c2a6ff93-2222-4345-a244-5dd7766ed619', label: 'F3', type: 'function', group: 'simple_Join_4_elements', color: '#80ff80', level: 1}
  ,{id: 'cada1271-5ed0-478b-98eb-15a52bb95a8b', label: 'xor', type: 'operator', group: 'simple_Join_4_elements', color: 'gray', level: 2}
  ,{id: 'd5f40c60-cf9f-492a-ab4e-2a9fc4d97b7d', label: 'E1', type: 'event', group: 'simple_Join_4_elements', color: '#FF8080', level: 3}
  ,{id: 'ae0b435a-2f3c-4b10-89f8-2726881094e7', label: 'F4', type: 'function', group: 'simple_Join_4_elements', color: '#80ff80', level: 1}
  ,{id: 'de721188-9c8e-4e42-8d8e-4db771949ef3', label: 'F2', type: 'function', group: 'simple_Join_4_elements', color: '#80ff80', level: 1}
  ,{id: '7ffefc47-d97a-4fe6-8ba7-fb5dfce2208a', label: 'F1', type: 'function', group: 'simple_Join_4_elements', color: '#80ff80', level: 1}
  ]; edges = [{from: 'c2a6ff93-2222-4345-a244-5dd7766ed619', to: 'cada1271-5ed0-478b-98eb-15a52bb95a8b', arrows:'to', style: 'arrow', color: 'gray'}
  ,{from: 'cada1271-5ed0-478b-98eb-15a52bb95a8b', to: 'd5f40c60-cf9f-492a-ab4e-2a9fc4d97b7d', arrows:'to', style: 'arrow', color: 'gray'}
  ,{from: 'ae0b435a-2f3c-4b10-89f8-2726881094e7', to: 'cada1271-5ed0-478b-98eb-15a52bb95a8b', arrows:'to', style: 'arrow', color: 'gray'}
  ,{from: 'de721188-9c8e-4e42-8d8e-4db771949ef3', to: 'cada1271-5ed0-478b-98eb-15a52bb95a8b', arrows:'to', style: 'arrow', color: 'gray'}
  ,{from: '7ffefc47-d97a-4fe6-8ba7-fb5dfce2208a', to: 'cada1271-5ed0-478b-98eb-15a52bb95a8b', arrows:'to', style: 'arrow', color: 'gray'}
  ];
		  var satelliteObjects = [];
		  var relations = [];


		  var musterErgebnis = "node xor: (0, 0)node E1: (0, 60)node F2: (50, -60)node F4: (-50, -60)node F3: (-150, -60)node F1: (150, -60)F3 -> xor: [(-150, -35)(-150, 0)(-25, 0)]xor -> E1: [(0, 25)(0, 35)]F4 -> xor: [(-50, -35)(-50, 0)(-25, 0)]F2 -> xor: [(50, -35)(50, 0)(25, 0)]F1 -> xor: [(150, -35)(150, 0)(25, 0)]";



          expect(setNodesAndEdges("test", undefined, nodes, edges, satelliteObjects, relations, 0, 10, 0, 0, true)).toEqual(musterErgebnis);
      });
	
		  it("simple Join 5 elements [100, 50, 0, 0]", function () {
		
		  var nodes = [{id: '4a10f0cf-89f8-4a73-909b-8c2baf98f0e1', label: 'F5', type: 'function', group: 'simple_Join_5_elements', color: '#80ff80', level: 1}
  ,{id: '70eeb20d-21b2-4be4-9680-e6085e386ead', label: 'xor', type: 'operator', group: 'simple_Join_5_elements', color: 'gray', level: 2}
  ,{id: '100d1ce2-e271-4fda-9878-73b6b1f7dd07', label: 'E1', type: 'event', group: 'simple_Join_5_elements', color: '#FF8080', level: 3}
  ,{id: 'f2d6a1ad-a253-4564-9ba2-b564831e9672', label: 'F3', type: 'function', group: 'simple_Join_5_elements', color: '#80ff80', level: 1}
  ,{id: '8866f12b-a44c-4a56-b7d7-d6e38f846aaf', label: 'F1', type: 'function', group: 'simple_Join_5_elements', color: '#80ff80', level: 1}
  ,{id: '1e6a68d4-286e-4208-9ca3-f68292d6a630', label: 'F2', type: 'function', group: 'simple_Join_5_elements', color: '#80ff80', level: 1}
  ,{id: '401cd94f-f042-45d7-8cf9-8179e2cc4ffb', label: 'F4', type: 'function', group: 'simple_Join_5_elements', color: '#80ff80', level: 1}
  ]; edges = [{from: '4a10f0cf-89f8-4a73-909b-8c2baf98f0e1', to: '70eeb20d-21b2-4be4-9680-e6085e386ead', arrows:'to', style: 'arrow', color: 'gray'}
  ,{from: '70eeb20d-21b2-4be4-9680-e6085e386ead', to: '100d1ce2-e271-4fda-9878-73b6b1f7dd07', arrows:'to', style: 'arrow', color: 'gray'}
  ,{from: 'f2d6a1ad-a253-4564-9ba2-b564831e9672', to: '70eeb20d-21b2-4be4-9680-e6085e386ead', arrows:'to', style: 'arrow', color: 'gray'}
  ,{from: '8866f12b-a44c-4a56-b7d7-d6e38f846aaf', to: '70eeb20d-21b2-4be4-9680-e6085e386ead', arrows:'to', style: 'arrow', color: 'gray'}
  ,{from: '1e6a68d4-286e-4208-9ca3-f68292d6a630', to: '70eeb20d-21b2-4be4-9680-e6085e386ead', arrows:'to', style: 'arrow', color: 'gray'}
  ,{from: '401cd94f-f042-45d7-8cf9-8179e2cc4ffb', to: '70eeb20d-21b2-4be4-9680-e6085e386ead', arrows:'to', style: 'arrow', color: 'gray'}
  ];
		  var satelliteObjects = [];
		  var relations = [];


		  var musterErgebnis = "node xor: (0, 0)node E1: (0, 100)node F1: (0, -100)node F3: (-200, -100)node F5: (-400, -100)node F2: (200, -100)node F4: (400, -100)F5 -> xor: [(-400, -75)(-400, 0)(-25, 0)]xor -> E1: [(0, 25)(0, 75)]F3 -> xor: [(-200, -75)(-200, 0)(-25, 0)]F1 -> xor: [(0, -75)(0, -25)]F2 -> xor: [(200, -75)(200, 0)(25, 0)]F4 -> xor: [(400, -75)(400, 0)(25, 0)]";



          expect(setNodesAndEdges("test", undefined, nodes, edges, satelliteObjects, relations, 100, 50, 0, 0, true)).toEqual(musterErgebnis);
      });
	
	  it("simple Join 5 elements [0, 10, 0, 0]", function () {
		
		  var nodes = [{id: '4a10f0cf-89f8-4a73-909b-8c2baf98f0e1', label: 'F5', type: 'function', group: 'simple_Join_5_elements', color: '#80ff80', level: 1}
  ,{id: '70eeb20d-21b2-4be4-9680-e6085e386ead', label: 'xor', type: 'operator', group: 'simple_Join_5_elements', color: 'gray', level: 2}
  ,{id: '100d1ce2-e271-4fda-9878-73b6b1f7dd07', label: 'E1', type: 'event', group: 'simple_Join_5_elements', color: '#FF8080', level: 3}
  ,{id: 'f2d6a1ad-a253-4564-9ba2-b564831e9672', label: 'F3', type: 'function', group: 'simple_Join_5_elements', color: '#80ff80', level: 1}
  ,{id: '8866f12b-a44c-4a56-b7d7-d6e38f846aaf', label: 'F1', type: 'function', group: 'simple_Join_5_elements', color: '#80ff80', level: 1}
  ,{id: '1e6a68d4-286e-4208-9ca3-f68292d6a630', label: 'F2', type: 'function', group: 'simple_Join_5_elements', color: '#80ff80', level: 1}
  ,{id: '401cd94f-f042-45d7-8cf9-8179e2cc4ffb', label: 'F4', type: 'function', group: 'simple_Join_5_elements', color: '#80ff80', level: 1}
  ]; edges = [{from: '4a10f0cf-89f8-4a73-909b-8c2baf98f0e1', to: '70eeb20d-21b2-4be4-9680-e6085e386ead', arrows:'to', style: 'arrow', color: 'gray'}
  ,{from: '70eeb20d-21b2-4be4-9680-e6085e386ead', to: '100d1ce2-e271-4fda-9878-73b6b1f7dd07', arrows:'to', style: 'arrow', color: 'gray'}
  ,{from: 'f2d6a1ad-a253-4564-9ba2-b564831e9672', to: '70eeb20d-21b2-4be4-9680-e6085e386ead', arrows:'to', style: 'arrow', color: 'gray'}
  ,{from: '8866f12b-a44c-4a56-b7d7-d6e38f846aaf', to: '70eeb20d-21b2-4be4-9680-e6085e386ead', arrows:'to', style: 'arrow', color: 'gray'}
  ,{from: '1e6a68d4-286e-4208-9ca3-f68292d6a630', to: '70eeb20d-21b2-4be4-9680-e6085e386ead', arrows:'to', style: 'arrow', color: 'gray'}
  ,{from: '401cd94f-f042-45d7-8cf9-8179e2cc4ffb', to: '70eeb20d-21b2-4be4-9680-e6085e386ead', arrows:'to', style: 'arrow', color: 'gray'}
  ];
		  var satelliteObjects = [];
		  var relations = [];


		  var musterErgebnis = "node xor: (0, 0)node E1: (0, 60)node F1: (0, -60)node F3: (-100, -60)node F5: (-200, -60)node F2: (100, -60)node F4: (200, -60)F5 -> xor: [(-200, -35)(-200, 0)(-25, 0)]xor -> E1: [(0, 25)(0, 35)]F3 -> xor: [(-100, -35)(-100, 0)(-25, 0)]F1 -> xor: [(0, -35)(0, -25)]F2 -> xor: [(100, -35)(100, 0)(25, 0)]F4 -> xor: [(200, -35)(200, 0)(25, 0)]";



          expect(setNodesAndEdges("test", undefined, nodes, edges, satelliteObjects, relations, 0, 10, 0, 0, true)).toEqual(musterErgebnis);
      });
	
		  it("simple Split 2 elements [100, 50, 0, 0]", function () {
		
		  var nodes = [{id: '00662f38-6f0d-4f0a-92ce-df3dcf387cba', label: 'F1', type: 'function', group: 'simple_Split_2_elements', color: '#80ff80', level: 1}
  ,{id: 'cb84ca6d-4bff-4adb-b473-2ab5a10ae873', label: 'xor', type: 'operator', group: 'simple_Split_2_elements', color: 'gray', level: 2}
  ,{id: 'a2fbde10-e636-4a0b-b0a5-45a96d5dec2c', label: 'F2', type: 'function', group: 'simple_Split_2_elements', color: '#80ff80', level: 3}
  ,{id: '6ef6acff-46fd-44d1-9bb1-991f3ad32aed', label: 'F3', type: 'function', group: 'simple_Split_2_elements', color: '#80ff80', level: 3}
  ]; edges = [{from: '00662f38-6f0d-4f0a-92ce-df3dcf387cba', to: 'cb84ca6d-4bff-4adb-b473-2ab5a10ae873', arrows:'to', style: 'arrow', color: 'gray'}
  ,{from: 'cb84ca6d-4bff-4adb-b473-2ab5a10ae873', to: 'a2fbde10-e636-4a0b-b0a5-45a96d5dec2c', arrows:'to', style: 'arrow', color: 'gray'}
  ,{from: 'cb84ca6d-4bff-4adb-b473-2ab5a10ae873', to: '6ef6acff-46fd-44d1-9bb1-991f3ad32aed', arrows:'to', style: 'arrow', color: 'gray'}
  ];
		  var satelliteObjects = [];
		  var relations = [];


		  var musterErgebnis = "node F1: (0, 0)node xor: (0, 100)node F2: (-100, 200)node F3: (100, 200)F1 -> xor: [(0, 25)(0, 75)]xor -> F2: [(-25, 100)(-100, 100)(-100, 175)]xor -> F3: [(25, 100)(100, 100)(100, 175)]";



          expect(setNodesAndEdges("test", undefined, nodes, edges, satelliteObjects, relations, 100, 50, 0, 0, true)).toEqual(musterErgebnis);
      });
	
	  it("simple Split 2 elements [0, 10, 0, 0]", function () {
		
		  var nodes = [{id: '00662f38-6f0d-4f0a-92ce-df3dcf387cba', label: 'F1', type: 'function', group: 'simple_Split_2_elements', color: '#80ff80', level: 1}
  ,{id: 'cb84ca6d-4bff-4adb-b473-2ab5a10ae873', label: 'xor', type: 'operator', group: 'simple_Split_2_elements', color: 'gray', level: 2}
  ,{id: 'a2fbde10-e636-4a0b-b0a5-45a96d5dec2c', label: 'F2', type: 'function', group: 'simple_Split_2_elements', color: '#80ff80', level: 3}
  ,{id: '6ef6acff-46fd-44d1-9bb1-991f3ad32aed', label: 'F3', type: 'function', group: 'simple_Split_2_elements', color: '#80ff80', level: 3}
  ]; edges = [{from: '00662f38-6f0d-4f0a-92ce-df3dcf387cba', to: 'cb84ca6d-4bff-4adb-b473-2ab5a10ae873', arrows:'to', style: 'arrow', color: 'gray'}
  ,{from: 'cb84ca6d-4bff-4adb-b473-2ab5a10ae873', to: 'a2fbde10-e636-4a0b-b0a5-45a96d5dec2c', arrows:'to', style: 'arrow', color: 'gray'}
  ,{from: 'cb84ca6d-4bff-4adb-b473-2ab5a10ae873', to: '6ef6acff-46fd-44d1-9bb1-991f3ad32aed', arrows:'to', style: 'arrow', color: 'gray'}
  ];
		  var satelliteObjects = [];
		  var relations = [];


		  var musterErgebnis = "node F1: (0, 0)node xor: (0, 60)node F2: (-50, 120)node F3: (50, 120)F1 -> xor: [(0, 25)(0, 35)]xor -> F2: [(-25, 60)(-50, 60)(-50, 95)]xor -> F3: [(25, 60)(50, 60)(50, 95)]";



          expect(setNodesAndEdges("test", undefined, nodes, edges, satelliteObjects, relations, 0, 10, 0, 0, true)).toEqual(musterErgebnis);
      });
	
		  it("simple Split 3 elements [100, 50, 0, 0]", function () {
		
		  var nodes = [{id: 'b5add039-0ab3-4911-af83-d3013a3ea0d4', label: 'F1', type: 'function', group: 'simple_Split_3_elements', color: '#80ff80', level: 1}
  ,{id: 'b44f7dd6-9187-4e0e-b8d2-824fa1e10efe', label: 'xor', type: 'operator', group: 'simple_Split_3_elements', color: 'gray', level: 2}
  ,{id: 'fc127491-ca52-4a8f-b6db-fa46a43c89be', label: 'F2', type: 'function', group: 'simple_Split_3_elements', color: '#80ff80', level: 3}
  ,{id: '31fcbe9b-59d4-4635-811a-387b7f096470', label: 'F3', type: 'function', group: 'simple_Split_3_elements', color: '#80ff80', level: 3}
  ,{id: '68f40974-7e39-4e19-9dcf-c2cbb5a14ebd', label: 'F4', type: 'function', group: 'simple_Split_3_elements', color: '#80ff80', level: 3}
  ]; edges = [{from: 'b5add039-0ab3-4911-af83-d3013a3ea0d4', to: 'b44f7dd6-9187-4e0e-b8d2-824fa1e10efe', arrows:'to', style: 'arrow', color: 'gray'}
  ,{from: 'b44f7dd6-9187-4e0e-b8d2-824fa1e10efe', to: 'fc127491-ca52-4a8f-b6db-fa46a43c89be', arrows:'to', style: 'arrow', color: 'gray'}
  ,{from: 'b44f7dd6-9187-4e0e-b8d2-824fa1e10efe', to: '31fcbe9b-59d4-4635-811a-387b7f096470', arrows:'to', style: 'arrow', color: 'gray'}
  ,{from: 'b44f7dd6-9187-4e0e-b8d2-824fa1e10efe', to: '68f40974-7e39-4e19-9dcf-c2cbb5a14ebd', arrows:'to', style: 'arrow', color: 'gray'}
  ];
		  var satelliteObjects = [];
		  var relations = [];


		  var musterErgebnis = "node F1: (0, 0)node xor: (0, 100)node F2: (-200, 200)node F3: (0, 200)node F4: (200, 200)F1 -> xor: [(0, 25)(0, 75)]xor -> F2: [(-25, 100)(-200, 100)(-200, 175)]xor -> F3: [(0, 125)(0, 175)]xor -> F4: [(25, 100)(200, 100)(200, 175)]";



          expect(setNodesAndEdges("test", undefined, nodes, edges, satelliteObjects, relations, 100, 50, 0, 0, true)).toEqual(musterErgebnis);
      });
	
	  it("simple Split 3 elements [0, 10, 0, 0]", function () {
		
		  var nodes = [{id: 'b5add039-0ab3-4911-af83-d3013a3ea0d4', label: 'F1', type: 'function', group: 'simple_Split_3_elements', color: '#80ff80', level: 1}
  ,{id: 'b44f7dd6-9187-4e0e-b8d2-824fa1e10efe', label: 'xor', type: 'operator', group: 'simple_Split_3_elements', color: 'gray', level: 2}
  ,{id: 'fc127491-ca52-4a8f-b6db-fa46a43c89be', label: 'F2', type: 'function', group: 'simple_Split_3_elements', color: '#80ff80', level: 3}
  ,{id: '31fcbe9b-59d4-4635-811a-387b7f096470', label: 'F3', type: 'function', group: 'simple_Split_3_elements', color: '#80ff80', level: 3}
  ,{id: '68f40974-7e39-4e19-9dcf-c2cbb5a14ebd', label: 'F4', type: 'function', group: 'simple_Split_3_elements', color: '#80ff80', level: 3}
  ]; edges = [{from: 'b5add039-0ab3-4911-af83-d3013a3ea0d4', to: 'b44f7dd6-9187-4e0e-b8d2-824fa1e10efe', arrows:'to', style: 'arrow', color: 'gray'}
  ,{from: 'b44f7dd6-9187-4e0e-b8d2-824fa1e10efe', to: 'fc127491-ca52-4a8f-b6db-fa46a43c89be', arrows:'to', style: 'arrow', color: 'gray'}
  ,{from: 'b44f7dd6-9187-4e0e-b8d2-824fa1e10efe', to: '31fcbe9b-59d4-4635-811a-387b7f096470', arrows:'to', style: 'arrow', color: 'gray'}
  ,{from: 'b44f7dd6-9187-4e0e-b8d2-824fa1e10efe', to: '68f40974-7e39-4e19-9dcf-c2cbb5a14ebd', arrows:'to', style: 'arrow', color: 'gray'}
  ];
		  var satelliteObjects = [];
		  var relations = [];


		  var musterErgebnis = "node F1: (0, 0)node xor: (0, 60)node F2: (-100, 120)node F3: (0, 120)node F4: (100, 120)F1 -> xor: [(0, 25)(0, 35)]xor -> F2: [(-25, 60)(-100, 60)(-100, 95)]xor -> F3: [(0, 85)(0, 95)]xor -> F4: [(25, 60)(100, 60)(100, 95)]";



          expect(setNodesAndEdges("test", undefined, nodes, edges, satelliteObjects, relations, 0, 10, 0, 0, true)).toEqual(musterErgebnis);
      });
	
		  it("simple Split 4 elements [100, 50, 0, 0]", function () {
		
		  var nodes = [{id: 'c217dd3f-bf1b-4f69-8769-4bd6153fd88a', label: 'F1', type: 'function', group: 'simple_Split_4_elements', color: '#80ff80', level: 1}
  ,{id: 'bbfcd124-c00c-44ee-b5bb-5ce7282e8677', label: 'xor', type: 'operator', group: 'simple_Split_4_elements', color: 'gray', level: 2}
  ,{id: '34ebc3cb-4b23-4364-b01e-63bb2f459c9a', label: 'F2', type: 'function', group: 'simple_Split_4_elements', color: '#80ff80', level: 3}
  ,{id: '4e435989-cad5-47a9-b8dc-f235d61832ac', label: 'F3', type: 'function', group: 'simple_Split_4_elements', color: '#80ff80', level: 3}
  ,{id: 'cfd36a8e-88b9-40ee-b140-d209c2c0b607', label: 'F4', type: 'function', group: 'simple_Split_4_elements', color: '#80ff80', level: 3}
  ,{id: '948a22cf-4699-4fca-ac8d-5b7178722194', label: 'F5', type: 'function', group: 'simple_Split_4_elements', color: '#80ff80', level: 3}
  ]; edges = [{from: 'bbfcd124-c00c-44ee-b5bb-5ce7282e8677', to: '34ebc3cb-4b23-4364-b01e-63bb2f459c9a', arrows:'to', style: 'arrow', color: 'gray'}
  ,{from: 'bbfcd124-c00c-44ee-b5bb-5ce7282e8677', to: '4e435989-cad5-47a9-b8dc-f235d61832ac', arrows:'to', style: 'arrow', color: 'gray'}
  ,{from: 'bbfcd124-c00c-44ee-b5bb-5ce7282e8677', to: 'cfd36a8e-88b9-40ee-b140-d209c2c0b607', arrows:'to', style: 'arrow', color: 'gray'}
  ,{from: 'bbfcd124-c00c-44ee-b5bb-5ce7282e8677', to: '948a22cf-4699-4fca-ac8d-5b7178722194', arrows:'to', style: 'arrow', color: 'gray'}
  ,{from: 'c217dd3f-bf1b-4f69-8769-4bd6153fd88a', to: 'bbfcd124-c00c-44ee-b5bb-5ce7282e8677', arrows:'to', style: 'arrow', color: 'gray'}
  ];
		  var satelliteObjects = [];
		  var relations = [];


		  var musterErgebnis = "node F1: (0, 0)node xor: (0, 100)node F2: (-300, 200)node F3: (-100, 200)node F4: (100, 200)node F5: (300, 200)xor -> F2: [(-25, 100)(-300, 100)(-300, 175)]xor -> F3: [(-25, 100)(-100, 100)(-100, 175)]xor -> F4: [(25, 100)(100, 100)(100, 175)]xor -> F5: [(25, 100)(300, 100)(300, 175)]F1 -> xor: [(0, 25)(0, 75)]";



          expect(setNodesAndEdges("test", undefined, nodes, edges, satelliteObjects, relations, 100, 50, 0, 0, true)).toEqual(musterErgebnis);
      });
	
	  it("simple Split 4 elements [0, 10, 0, 0]", function () {
		
		  var nodes = [{id: 'c217dd3f-bf1b-4f69-8769-4bd6153fd88a', label: 'F1', type: 'function', group: 'simple_Split_4_elements', color: '#80ff80', level: 1}
  ,{id: 'bbfcd124-c00c-44ee-b5bb-5ce7282e8677', label: 'xor', type: 'operator', group: 'simple_Split_4_elements', color: 'gray', level: 2}
  ,{id: '34ebc3cb-4b23-4364-b01e-63bb2f459c9a', label: 'F2', type: 'function', group: 'simple_Split_4_elements', color: '#80ff80', level: 3}
  ,{id: '4e435989-cad5-47a9-b8dc-f235d61832ac', label: 'F3', type: 'function', group: 'simple_Split_4_elements', color: '#80ff80', level: 3}
  ,{id: 'cfd36a8e-88b9-40ee-b140-d209c2c0b607', label: 'F4', type: 'function', group: 'simple_Split_4_elements', color: '#80ff80', level: 3}
  ,{id: '948a22cf-4699-4fca-ac8d-5b7178722194', label: 'F5', type: 'function', group: 'simple_Split_4_elements', color: '#80ff80', level: 3}
  ]; edges = [{from: 'bbfcd124-c00c-44ee-b5bb-5ce7282e8677', to: '34ebc3cb-4b23-4364-b01e-63bb2f459c9a', arrows:'to', style: 'arrow', color: 'gray'}
  ,{from: 'bbfcd124-c00c-44ee-b5bb-5ce7282e8677', to: '4e435989-cad5-47a9-b8dc-f235d61832ac', arrows:'to', style: 'arrow', color: 'gray'}
  ,{from: 'bbfcd124-c00c-44ee-b5bb-5ce7282e8677', to: 'cfd36a8e-88b9-40ee-b140-d209c2c0b607', arrows:'to', style: 'arrow', color: 'gray'}
  ,{from: 'bbfcd124-c00c-44ee-b5bb-5ce7282e8677', to: '948a22cf-4699-4fca-ac8d-5b7178722194', arrows:'to', style: 'arrow', color: 'gray'}
  ,{from: 'c217dd3f-bf1b-4f69-8769-4bd6153fd88a', to: 'bbfcd124-c00c-44ee-b5bb-5ce7282e8677', arrows:'to', style: 'arrow', color: 'gray'}
  ];
		  var satelliteObjects = [];
		  var relations = [];


		  var musterErgebnis = "node F1: (0, 0)node xor: (0, 60)node F2: (-150, 120)node F3: (-50, 120)node F4: (50, 120)node F5: (150, 120)xor -> F2: [(-25, 60)(-150, 60)(-150, 95)]xor -> F3: [(-25, 60)(-50, 60)(-50, 95)]xor -> F4: [(25, 60)(50, 60)(50, 95)]xor -> F5: [(25, 60)(150, 60)(150, 95)]F1 -> xor: [(0, 25)(0, 35)]";



          expect(setNodesAndEdges("test", undefined, nodes, edges, satelliteObjects, relations, 0, 10, 0, 0, true)).toEqual(musterErgebnis);
      });
	
		 it("simple Split 5 elements [100, 50, 0, 0]", function () {
		
		 var nodes = [{id: 'b932d1b2-572e-45fb-bf3d-3626b6bd58c1', label: 'F1', type: 'function', group: 'simple_Split_5_elements', color: '#80ff80', level: 1}
 ,{id: '64b741c3-ea77-4df8-8d85-2053a2fd42a8', label: 'xor', type: 'operator', group: 'simple_Split_5_elements', color: 'gray', level: 2}
 ,{id: 'a9aff8f9-fea0-4ad7-97d7-4829945d9f23', label: 'F5', type: 'function', group: 'simple_Split_5_elements', color: '#80ff80', level: 3}
 ,{id: 'f9fa769a-642f-44ac-88bf-060a72b302fd', label: 'F6', type: 'function', group: 'simple_Split_5_elements', color: '#80ff80', level: 3}
 ,{id: '1bc3221f-62f2-40bf-b8d7-be36a2d6e33c', label: 'F3', type: 'function', group: 'simple_Split_5_elements', color: '#80ff80', level: 3}
 ,{id: '3b01ea2e-875c-4a76-91a0-7d8ffb1131cb', label: 'F2', type: 'function', group: 'simple_Split_5_elements', color: '#80ff80', level: 3}
 ,{id: 'f6b36c30-2444-4872-bdad-a5cb89a0ae3e', label: 'F4', type: 'function', group: 'simple_Split_5_elements', color: '#80ff80', level: 3}
 ]; edges = [{from: '64b741c3-ea77-4df8-8d85-2053a2fd42a8', to: 'a9aff8f9-fea0-4ad7-97d7-4829945d9f23', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: '64b741c3-ea77-4df8-8d85-2053a2fd42a8', to: 'f9fa769a-642f-44ac-88bf-060a72b302fd', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: '64b741c3-ea77-4df8-8d85-2053a2fd42a8', to: '1bc3221f-62f2-40bf-b8d7-be36a2d6e33c', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: '64b741c3-ea77-4df8-8d85-2053a2fd42a8', to: '3b01ea2e-875c-4a76-91a0-7d8ffb1131cb', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: '64b741c3-ea77-4df8-8d85-2053a2fd42a8', to: 'f6b36c30-2444-4872-bdad-a5cb89a0ae3e', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: 'b932d1b2-572e-45fb-bf3d-3626b6bd58c1', to: '64b741c3-ea77-4df8-8d85-2053a2fd42a8', arrows:'to', style: 'arrow', color: 'gray'}
 ];
		 var satelliteObjects = [];
		 var relations = [];


		 var musterErgebnis = "node F1: (0, 0)node xor: (0, 100)node F5: (-400, 200)node F6: (-200, 200)node F3: (0, 200)node F2: (200, 200)node F4: (400, 200)xor -> F5: [(-25, 100)(-400, 100)(-400, 175)]xor -> F6: [(-25, 100)(-200, 100)(-200, 175)]xor -> F3: [(0, 125)(0, 175)]xor -> F2: [(25, 100)(200, 100)(200, 175)]xor -> F4: [(25, 100)(400, 100)(400, 175)]F1 -> xor: [(0, 25)(0, 75)]";



         expect(setNodesAndEdges("test", undefined, nodes, edges, satelliteObjects, relations, 100, 50, 0, 0, true)).toEqual(musterErgebnis);
     });
	
	 it("simple Split 5 elements [0, 10, 0, 0]", function () {
		
		 var nodes = [{id: 'b932d1b2-572e-45fb-bf3d-3626b6bd58c1', label: 'F1', type: 'function', group: 'simple_Split_5_elements', color: '#80ff80', level: 1}
 ,{id: '64b741c3-ea77-4df8-8d85-2053a2fd42a8', label: 'xor', type: 'operator', group: 'simple_Split_5_elements', color: 'gray', level: 2}
 ,{id: 'a9aff8f9-fea0-4ad7-97d7-4829945d9f23', label: 'F5', type: 'function', group: 'simple_Split_5_elements', color: '#80ff80', level: 3}
 ,{id: 'f9fa769a-642f-44ac-88bf-060a72b302fd', label: 'F6', type: 'function', group: 'simple_Split_5_elements', color: '#80ff80', level: 3}
 ,{id: '1bc3221f-62f2-40bf-b8d7-be36a2d6e33c', label: 'F3', type: 'function', group: 'simple_Split_5_elements', color: '#80ff80', level: 3}
 ,{id: '3b01ea2e-875c-4a76-91a0-7d8ffb1131cb', label: 'F2', type: 'function', group: 'simple_Split_5_elements', color: '#80ff80', level: 3}
 ,{id: 'f6b36c30-2444-4872-bdad-a5cb89a0ae3e', label: 'F4', type: 'function', group: 'simple_Split_5_elements', color: '#80ff80', level: 3}
 ]; edges = [{from: '64b741c3-ea77-4df8-8d85-2053a2fd42a8', to: 'a9aff8f9-fea0-4ad7-97d7-4829945d9f23', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: '64b741c3-ea77-4df8-8d85-2053a2fd42a8', to: 'f9fa769a-642f-44ac-88bf-060a72b302fd', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: '64b741c3-ea77-4df8-8d85-2053a2fd42a8', to: '1bc3221f-62f2-40bf-b8d7-be36a2d6e33c', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: '64b741c3-ea77-4df8-8d85-2053a2fd42a8', to: '3b01ea2e-875c-4a76-91a0-7d8ffb1131cb', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: '64b741c3-ea77-4df8-8d85-2053a2fd42a8', to: 'f6b36c30-2444-4872-bdad-a5cb89a0ae3e', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: 'b932d1b2-572e-45fb-bf3d-3626b6bd58c1', to: '64b741c3-ea77-4df8-8d85-2053a2fd42a8', arrows:'to', style: 'arrow', color: 'gray'}
 ];
		 var satelliteObjects = [];
		 var relations = [];


		 var musterErgebnis = "node F1: (0, 0)node xor: (0, 60)node F5: (-200, 120)node F6: (-100, 120)node F3: (0, 120)node F2: (100, 120)node F4: (200, 120)xor -> F5: [(-25, 60)(-200, 60)(-200, 95)]xor -> F6: [(-25, 60)(-100, 60)(-100, 95)]xor -> F3: [(0, 85)(0, 95)]xor -> F2: [(25, 60)(100, 60)(100, 95)]xor -> F4: [(25, 60)(200, 60)(200, 95)]F1 -> xor: [(0, 25)(0, 35)]";



         expect(setNodesAndEdges("test", undefined, nodes, edges, satelliteObjects, relations, 0, 10, 0, 0, true)).toEqual(musterErgebnis);
     });
	
		 it("EPC_SplitDannDirektJoin [100, 50, 0, 0]", function () {
		
		 var nodes = [{id: '1da13e7e-0227-4118-8b4f-6fe409d3f945', label: 'F1', type: 'function', group: 'EPC_SplitDannDirektJoin', color: '#80ff80', level: 1}
 ,{id: '9e568f78-95cf-4bb1-a777-083d09cc990a', label: 'or', type: 'operator', group: 'EPC_SplitDannDirektJoin', color: 'gray', level: 2}
 ,{id: 'a56e03e0-8969-44a7-aa7a-aa811d1d1ee5', label: 'F5', type: 'function', group: 'EPC_SplitDannDirektJoin', color: '#80ff80', level: 3}
 ,{id: '7ba33518-0d5f-41da-83df-e160b04345d1', label: 'F2', type: 'function', group: 'EPC_SplitDannDirektJoin', color: '#80ff80', level: 1}
 ,{id: 'f2ae5806-7275-462c-b8de-ca10de5e2160', label: 'xor', type: 'operator', group: 'EPC_SplitDannDirektJoin', color: 'gray', level: 2}
 ,{id: '16f2ab35-6242-4881-a752-6747f879b185', label: 'F4', type: 'function', group: 'EPC_SplitDannDirektJoin', color: '#80ff80', level: 3}
 ,{id: '3c6e2f71-2de9-4e4f-b183-110288920f6f', label: 'F3', type: 'function', group: 'EPC_SplitDannDirektJoin', color: '#80ff80', level: 3}
 ]; edges = [{from: '1da13e7e-0227-4118-8b4f-6fe409d3f945', to: '9e568f78-95cf-4bb1-a777-083d09cc990a', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: 'f2ae5806-7275-462c-b8de-ca10de5e2160', to: '16f2ab35-6242-4881-a752-6747f879b185', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: 'f2ae5806-7275-462c-b8de-ca10de5e2160', to: '3c6e2f71-2de9-4e4f-b183-110288920f6f', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: 'f2ae5806-7275-462c-b8de-ca10de5e2160', to: '9e568f78-95cf-4bb1-a777-083d09cc990a', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: '7ba33518-0d5f-41da-83df-e160b04345d1', to: 'f2ae5806-7275-462c-b8de-ca10de5e2160', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: '9e568f78-95cf-4bb1-a777-083d09cc990a', to: 'a56e03e0-8969-44a7-aa7a-aa811d1d1ee5', arrows:'to', style: 'arrow', color: 'gray'}
 ];
		 var satelliteObjects = [];
		 var relations = [];


		 var musterErgebnis = "node or: (0, 0)node F5: (0, 100)node F2: (200, -300)node xor: (200, -200)node F4: (100, -100)node F3: (300, -100)node F1: (-100, -100)F1 -> or: [(-100, -75)(-100, 0)(-25, 0)]xor -> F4: [(175, -200)(100, -200)(100, -125)]xor -> F3: [(225, -200)(300, -200)(300, -125)]xor -> or: [(200, -175)(200, 0)(25, 0)]F2 -> xor: [(200, -275)(200, -225)]or -> F5: [(0, 25)(0, 75)]";



         expect(setNodesAndEdges("test", undefined, nodes, edges, satelliteObjects, relations, 100, 50, 0, 0, true)).toEqual(musterErgebnis);
     });
	
	 it("EPC_SplitDannDirektJoin [0, 10, 0, 0]", function () {
		
		 var nodes = [{id: '1da13e7e-0227-4118-8b4f-6fe409d3f945', label: 'F1', type: 'function', group: 'EPC_SplitDannDirektJoin', color: '#80ff80', level: 1}
 ,{id: '9e568f78-95cf-4bb1-a777-083d09cc990a', label: 'or', type: 'operator', group: 'EPC_SplitDannDirektJoin', color: 'gray', level: 2}
 ,{id: 'a56e03e0-8969-44a7-aa7a-aa811d1d1ee5', label: 'F5', type: 'function', group: 'EPC_SplitDannDirektJoin', color: '#80ff80', level: 3}
 ,{id: '7ba33518-0d5f-41da-83df-e160b04345d1', label: 'F2', type: 'function', group: 'EPC_SplitDannDirektJoin', color: '#80ff80', level: 1}
 ,{id: 'f2ae5806-7275-462c-b8de-ca10de5e2160', label: 'xor', type: 'operator', group: 'EPC_SplitDannDirektJoin', color: 'gray', level: 2}
 ,{id: '16f2ab35-6242-4881-a752-6747f879b185', label: 'F4', type: 'function', group: 'EPC_SplitDannDirektJoin', color: '#80ff80', level: 3}
 ,{id: '3c6e2f71-2de9-4e4f-b183-110288920f6f', label: 'F3', type: 'function', group: 'EPC_SplitDannDirektJoin', color: '#80ff80', level: 3}
 ]; edges = [{from: '1da13e7e-0227-4118-8b4f-6fe409d3f945', to: '9e568f78-95cf-4bb1-a777-083d09cc990a', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: 'f2ae5806-7275-462c-b8de-ca10de5e2160', to: '16f2ab35-6242-4881-a752-6747f879b185', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: 'f2ae5806-7275-462c-b8de-ca10de5e2160', to: '3c6e2f71-2de9-4e4f-b183-110288920f6f', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: 'f2ae5806-7275-462c-b8de-ca10de5e2160', to: '9e568f78-95cf-4bb1-a777-083d09cc990a', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: '7ba33518-0d5f-41da-83df-e160b04345d1', to: 'f2ae5806-7275-462c-b8de-ca10de5e2160', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: '9e568f78-95cf-4bb1-a777-083d09cc990a', to: 'a56e03e0-8969-44a7-aa7a-aa811d1d1ee5', arrows:'to', style: 'arrow', color: 'gray'}
 ];
		 var satelliteObjects = [];
		 var relations = [];


		 var musterErgebnis = "node or: (0, 0)node F5: (0, 60)node F2: (100, -180)node xor: (100, -120)node F4: (50, -60)node F3: (150, -60)node F1: (-50, -60)F1 -> or: [(-50, -35)(-50, 0)(-25, 0)]xor -> F4: [(75, -120)(50, -120)(50, -85)]xor -> F3: [(125, -120)(150, -120)(150, -85)]xor -> or: [(100, -95)(100, 0)(25, 0)]F2 -> xor: [(100, -155)(100, -145)]or -> F5: [(0, 25)(0, 35)]";



         expect(setNodesAndEdges("test", undefined, nodes, edges, satelliteObjects, relations, 0, 10, 0, 0, true)).toEqual(musterErgebnis);
     });
	
		 it("EPC_SplitAndJoin [100, 50, 0, 0]", function () {
		
		 var nodes = [{id: '3a3b1aa9-58f5-40b0-b6cd-77703b301456', label: '1', type: 'function', group: 'EPC_SplitAndJoin', color: '#80ff80', level: 1}
 ,{id: '713bee1a-f678-45c6-bacb-59c299c4e8ef', label: '2', type: 'event', group: 'EPC_SplitAndJoin', color: '#FF8080', level: 2}
 ,{id: '2377eff6-f1c0-4f70-88e9-55703d0e36f6', label: '3', type: 'function', group: 'EPC_SplitAndJoin', color: '#80ff80', level: 3}
 ,{id: '1a927fdc-875d-408a-bcf7-f1a79dbb318b', label: 'xor', type: 'operator', group: 'EPC_SplitAndJoin', color: 'gray', level: 4}
 ,{id: '8dd3efeb-e756-4bd5-886e-551be8c9dea3', label: '4', type: 'function', group: 'EPC_SplitAndJoin', color: '#80ff80', level: 5}
 ,{id: 'bf77190c-a09a-4255-9634-cf91a17878d0', label: '5', type: 'function', group: 'EPC_SplitAndJoin', color: '#80ff80', level: 6}
 ,{id: '0cbbc98f-9102-4a91-a29c-a3564e730e0e', label: 'xor', type: 'operator', group: 'EPC_SplitAndJoin', color: 'gray', level: 7}
 ,{id: '30deb3fc-168f-4ba5-a41b-30eb6bb79cee', label: '7', type: 'function', group: 'EPC_SplitAndJoin', color: '#80ff80', level: 8}
 ,{id: '7e989fa2-e7bc-42e5-b0e5-29e7f508a5ba', label: '6', type: 'function', group: 'EPC_SplitAndJoin', color: '#80ff80', level: 5}
 ]; edges = [{from: '2377eff6-f1c0-4f70-88e9-55703d0e36f6', to: '1a927fdc-875d-408a-bcf7-f1a79dbb318b', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: '3a3b1aa9-58f5-40b0-b6cd-77703b301456', to: '713bee1a-f678-45c6-bacb-59c299c4e8ef', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: 'bf77190c-a09a-4255-9634-cf91a17878d0', to: '0cbbc98f-9102-4a91-a29c-a3564e730e0e', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: '8dd3efeb-e756-4bd5-886e-551be8c9dea3', to: 'bf77190c-a09a-4255-9634-cf91a17878d0', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: '713bee1a-f678-45c6-bacb-59c299c4e8ef', to: '2377eff6-f1c0-4f70-88e9-55703d0e36f6', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: '7e989fa2-e7bc-42e5-b0e5-29e7f508a5ba', to: '0cbbc98f-9102-4a91-a29c-a3564e730e0e', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: '0cbbc98f-9102-4a91-a29c-a3564e730e0e', to: '30deb3fc-168f-4ba5-a41b-30eb6bb79cee', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: '1a927fdc-875d-408a-bcf7-f1a79dbb318b', to: '8dd3efeb-e756-4bd5-886e-551be8c9dea3', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: '1a927fdc-875d-408a-bcf7-f1a79dbb318b', to: '7e989fa2-e7bc-42e5-b0e5-29e7f508a5ba', arrows:'to', style: 'arrow', color: 'gray'}
 ];
		 var satelliteObjects = [];
		 var relations = [];


		 var musterErgebnis = "node 1: (0, 0)node 2: (0, 100)node 3: (0, 200)node xor: (0, 300)node 4: (-100, 400)node 5: (-100, 500)node 6: (100, 400)node xor: (0, 600)node 7: (0, 700)3 -> xor: [(0, 225)(0, 275)]1 -> 2: [(0, 25)(0, 75)]5 -> xor: [(-100, 525)(-100, 600)(-25, 600)]4 -> 5: [(-100, 425)(-100, 475)]2 -> 3: [(0, 125)(0, 175)]6 -> xor: [(100, 425)(100, 600)(25, 600)]xor -> 7: [(0, 625)(0, 675)]xor -> 4: [(-25, 300)(-100, 300)(-100, 375)]xor -> 6: [(25, 300)(100, 300)(100, 375)]";



         expect(setNodesAndEdges("test", undefined, nodes, edges, satelliteObjects, relations, 100, 50, 0, 0, true)).toEqual(musterErgebnis);
     });
	
	 it("EPC_SplitAndJoin [0, 10, 0, 0]", function () {
		
		 var nodes = [{id: '3a3b1aa9-58f5-40b0-b6cd-77703b301456', label: '1', type: 'function', group: 'EPC_SplitAndJoin', color: '#80ff80', level: 1}
 ,{id: '713bee1a-f678-45c6-bacb-59c299c4e8ef', label: '2', type: 'event', group: 'EPC_SplitAndJoin', color: '#FF8080', level: 2}
 ,{id: '2377eff6-f1c0-4f70-88e9-55703d0e36f6', label: '3', type: 'function', group: 'EPC_SplitAndJoin', color: '#80ff80', level: 3}
 ,{id: '1a927fdc-875d-408a-bcf7-f1a79dbb318b', label: 'xor', type: 'operator', group: 'EPC_SplitAndJoin', color: 'gray', level: 4}
 ,{id: '8dd3efeb-e756-4bd5-886e-551be8c9dea3', label: '4', type: 'function', group: 'EPC_SplitAndJoin', color: '#80ff80', level: 5}
 ,{id: 'bf77190c-a09a-4255-9634-cf91a17878d0', label: '5', type: 'function', group: 'EPC_SplitAndJoin', color: '#80ff80', level: 6}
 ,{id: '0cbbc98f-9102-4a91-a29c-a3564e730e0e', label: 'xor', type: 'operator', group: 'EPC_SplitAndJoin', color: 'gray', level: 7}
 ,{id: '30deb3fc-168f-4ba5-a41b-30eb6bb79cee', label: '7', type: 'function', group: 'EPC_SplitAndJoin', color: '#80ff80', level: 8}
 ,{id: '7e989fa2-e7bc-42e5-b0e5-29e7f508a5ba', label: '6', type: 'function', group: 'EPC_SplitAndJoin', color: '#80ff80', level: 5}
 ]; edges = [{from: '2377eff6-f1c0-4f70-88e9-55703d0e36f6', to: '1a927fdc-875d-408a-bcf7-f1a79dbb318b', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: '3a3b1aa9-58f5-40b0-b6cd-77703b301456', to: '713bee1a-f678-45c6-bacb-59c299c4e8ef', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: 'bf77190c-a09a-4255-9634-cf91a17878d0', to: '0cbbc98f-9102-4a91-a29c-a3564e730e0e', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: '8dd3efeb-e756-4bd5-886e-551be8c9dea3', to: 'bf77190c-a09a-4255-9634-cf91a17878d0', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: '713bee1a-f678-45c6-bacb-59c299c4e8ef', to: '2377eff6-f1c0-4f70-88e9-55703d0e36f6', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: '7e989fa2-e7bc-42e5-b0e5-29e7f508a5ba', to: '0cbbc98f-9102-4a91-a29c-a3564e730e0e', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: '0cbbc98f-9102-4a91-a29c-a3564e730e0e', to: '30deb3fc-168f-4ba5-a41b-30eb6bb79cee', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: '1a927fdc-875d-408a-bcf7-f1a79dbb318b', to: '8dd3efeb-e756-4bd5-886e-551be8c9dea3', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: '1a927fdc-875d-408a-bcf7-f1a79dbb318b', to: '7e989fa2-e7bc-42e5-b0e5-29e7f508a5ba', arrows:'to', style: 'arrow', color: 'gray'}
 ];
		 var satelliteObjects = [];
		 var relations = [];


		 var musterErgebnis = "node 1: (0, 0)node 2: (0, 60)node 3: (0, 120)node xor: (0, 180)node 4: (-50, 240)node 5: (-50, 300)node 6: (50, 240)node xor: (0, 360)node 7: (0, 420)3 -> xor: [(0, 145)(0, 155)]1 -> 2: [(0, 25)(0, 35)]5 -> xor: [(-50, 325)(-50, 360)(-25, 360)]4 -> 5: [(-50, 265)(-50, 275)]2 -> 3: [(0, 85)(0, 95)]6 -> xor: [(50, 265)(50, 360)(25, 360)]xor -> 7: [(0, 385)(0, 395)]xor -> 4: [(-25, 180)(-50, 180)(-50, 215)]xor -> 6: [(25, 180)(50, 180)(50, 215)]";



         expect(setNodesAndEdges("test", undefined, nodes, edges, satelliteObjects, relations, 0, 10, 0, 0, true)).toEqual(musterErgebnis);
     });
	
		 it("EPC_3JoinAnd1Split [100, 50, 0, 0]", function () {
		
		 var nodes = [{id: 'de10d570-be9b-4b46-83c7-ac82c43fbea8', label: 'F_60', type: 'function', group: 'EPC_3JoinAnd1Split', color: '#80ff80', level: 1}
 ,{id: 'c26ec8db-fa88-4b21-9c32-cb882d953f25', label: 'F_50', type: 'function', group: 'EPC_3JoinAnd1Split', color: '#80ff80', level: 2}
 ,{id: 'e5bb55a8-fe1c-4c9d-b744-935c3170f85a', label: 'F_40', type: 'function', group: 'EPC_3JoinAnd1Split', color: '#80ff80', level: 3}
 ,{id: 'd328201e-a28c-4443-95a1-12a2c183307c', label: 'F_30', type: 'function', group: 'EPC_3JoinAnd1Split', color: '#80ff80', level: 4}
 ,{id: 'f8a1a1a1-bb5f-49ea-919c-ef058d71bfcc', label: 'F_20', type: 'function', group: 'EPC_3JoinAnd1Split', color: '#80ff80', level: 5}
 ,{id: '9ec372b6-5458-452d-8e98-25dded668b3b', label: 'F_10', type: 'function', group: 'EPC_3JoinAnd1Split', color: '#80ff80', level: 6}
 ,{id: 'd16f234a-af7c-4a33-9464-5d2f7f1902a0', label: 'or', type: 'operator', group: 'EPC_3JoinAnd1Split', color: 'gray', level: 7}
 ,{id: '7a564e93-c651-4700-ae37-97b875024183', label: 'xor', type: 'operator', group: 'EPC_3JoinAnd1Split', color: 'gray', level: 8}
 ,{id: '0534d417-d189-4943-8c3e-c10f5ecf1397', label: 'F_11', type: 'function', group: 'EPC_3JoinAnd1Split', color: '#80ff80', level: 9}
 ,{id: '3e6970ad-e510-42a8-9f9f-c782b17e97a6', label: 'F_6', type: 'function', group: 'EPC_3JoinAnd1Split', color: '#80ff80', level: 1}
 ,{id: '71a16686-5bc5-4f25-bdad-b0485ec9794f', label: 'xor', type: 'operator', group: 'EPC_3JoinAnd1Split', color: 'gray', level: 2}
 ,{id: 'd64877a1-cd8a-4ba5-b39a-69ba8dde0e80', label: 'F_1', type: 'function', group: 'EPC_3JoinAnd1Split', color: '#80ff80', level: 1}
 ,{id: 'a51d9fb4-d4ef-4c2f-93cc-73f058f5f44a', label: 'F_2', type: 'function', group: 'EPC_3JoinAnd1Split', color: '#80ff80', level: 2}
 ,{id: '8bad378c-350b-4419-a26a-04c718d1e3e0', label: 'and', type: 'operator', group: 'EPC_3JoinAnd1Split', color: 'gray', level: 3}
 ,{id: '5af0597b-1763-428f-b38d-f7e12405368d', label: 'F_4', type: 'function', group: 'EPC_3JoinAnd1Split', color: '#80ff80', level: 4}
 ,{id: '8c90e1b1-cd98-48fc-98c2-4f1680c233dc', label: 'F_5', type: 'function', group: 'EPC_3JoinAnd1Split', color: '#80ff80', level: 4}
 ,{id: 'a331c10a-97ce-473b-b199-b0921cd332a1', label: 'F_3', type: 'function', group: 'EPC_3JoinAnd1Split', color: '#80ff80', level: 4}
 ,{id: '3e95e316-8a30-42a0-a5ff-9cf899e656d7', label: 'F_7', type: 'function', group: 'EPC_3JoinAnd1Split', color: '#80ff80', level: 1}
 ,{id: 'd9a4033f-cfc6-4991-9906-648165d9fb7d', label: 'F_9', type: 'function', group: 'EPC_3JoinAnd1Split', color: '#80ff80', level: 1}
 ,{id: 'f5f84f91-949f-434c-9094-117a62b63177', label: 'F_8', type: 'function', group: 'EPC_3JoinAnd1Split', color: '#80ff80', level: 1}
 ]; edges = [{from: 'de10d570-be9b-4b46-83c7-ac82c43fbea8', to: 'c26ec8db-fa88-4b21-9c32-cb882d953f25', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: 'd328201e-a28c-4443-95a1-12a2c183307c', to: 'f8a1a1a1-bb5f-49ea-919c-ef058d71bfcc', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: 'e5bb55a8-fe1c-4c9d-b744-935c3170f85a', to: 'd328201e-a28c-4443-95a1-12a2c183307c', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: '3e6970ad-e510-42a8-9f9f-c782b17e97a6', to: '71a16686-5bc5-4f25-bdad-b0485ec9794f', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: 'd64877a1-cd8a-4ba5-b39a-69ba8dde0e80', to: 'a51d9fb4-d4ef-4c2f-93cc-73f058f5f44a', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: '3e95e316-8a30-42a0-a5ff-9cf899e656d7', to: '71a16686-5bc5-4f25-bdad-b0485ec9794f', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: 'd9a4033f-cfc6-4991-9906-648165d9fb7d', to: '71a16686-5bc5-4f25-bdad-b0485ec9794f', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: 'c26ec8db-fa88-4b21-9c32-cb882d953f25', to: 'e5bb55a8-fe1c-4c9d-b744-935c3170f85a', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: '71a16686-5bc5-4f25-bdad-b0485ec9794f', to: 'd16f234a-af7c-4a33-9464-5d2f7f1902a0', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: 'd16f234a-af7c-4a33-9464-5d2f7f1902a0', to: '7a564e93-c651-4700-ae37-97b875024183', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: 'a51d9fb4-d4ef-4c2f-93cc-73f058f5f44a', to: '8bad378c-350b-4419-a26a-04c718d1e3e0', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: 'f5f84f91-949f-434c-9094-117a62b63177', to: '71a16686-5bc5-4f25-bdad-b0485ec9794f', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: 'f8a1a1a1-bb5f-49ea-919c-ef058d71bfcc', to: '9ec372b6-5458-452d-8e98-25dded668b3b', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: '7a564e93-c651-4700-ae37-97b875024183', to: '0534d417-d189-4943-8c3e-c10f5ecf1397', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: '9ec372b6-5458-452d-8e98-25dded668b3b', to: 'd16f234a-af7c-4a33-9464-5d2f7f1902a0', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: '8bad378c-350b-4419-a26a-04c718d1e3e0', to: '5af0597b-1763-428f-b38d-f7e12405368d', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: '8bad378c-350b-4419-a26a-04c718d1e3e0', to: '8c90e1b1-cd98-48fc-98c2-4f1680c233dc', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: '8bad378c-350b-4419-a26a-04c718d1e3e0', to: 'a331c10a-97ce-473b-b199-b0921cd332a1', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: '5af0597b-1763-428f-b38d-f7e12405368d', to: '7a564e93-c651-4700-ae37-97b875024183', arrows:'to', style: 'arrow', color: 'gray'}
 ];
		 var satelliteObjects = [];
		 var relations = [];


		 var musterErgebnis = "node xor: (0, 0)node F_11: (0, 100)node F_1: (300, -400)node F_2: (300, -300)node and: (300, -200)node F_4: (100, -100)node F_5: (300, -100)node F_3: (500, -100)node or: (-200, -100)node F_60: (-100, -700)node F_50: (-100, -600)node F_40: (-100, -500)node F_30: (-100, -400)node F_20: (-100, -300)node F_10: (-100, -200)node xor: (-600, -200)node F_9: (-500, -300)node F_7: (-700, -300)node F_6: (-900, -300)node F_8: (-300, -300)F_60 -> F_50: [(-100, -675)(-100, -625)]F_30 -> F_20: [(-100, -375)(-100, -325)]F_40 -> F_30: [(-100, -475)(-100, -425)]F_6 -> xor: [(-900, -275)(-900, -200)(-625, -200)]F_1 -> F_2: [(300, -375)(300, -325)]F_7 -> xor: [(-700, -275)(-700, -200)(-625, -200)]F_9 -> xor: [(-500, -275)(-500, -200)(-575, -200)]F_50 -> F_40: [(-100, -575)(-100, -525)]xor -> or: [(-600, -175)(-600, -100)(-225, -100)]or -> xor: [(-200, -75)(-200, 0)(-25, 0)]F_2 -> and: [(300, -275)(300, -225)]F_8 -> xor: [(-300, -275)(-300, -200)(-575, -200)]F_20 -> F_10: [(-100, -275)(-100, -225)]xor -> F_11: [(0, 25)(0, 75)]F_10 -> or: [(-100, -175)(-100, -100)(-175, -100)]and -> F_4: [(275, -200)(100, -200)(100, -125)]and -> F_5: [(300, -175)(300, -125)]and -> F_3: [(325, -200)(500, -200)(500, -125)]F_4 -> xor: [(100, -75)(100, 0)(25, 0)]";



         expect(setNodesAndEdges("test", undefined, nodes, edges, satelliteObjects, relations, 100, 50, 0, 0, true)).toEqual(musterErgebnis);
     });
	
	 it("EPC_3JoinAnd1Split [0, 10, 0, 0]", function () {
		
		 var nodes = [{id: 'de10d570-be9b-4b46-83c7-ac82c43fbea8', label: 'F_60', type: 'function', group: 'EPC_3JoinAnd1Split', color: '#80ff80', level: 1}
 ,{id: 'c26ec8db-fa88-4b21-9c32-cb882d953f25', label: 'F_50', type: 'function', group: 'EPC_3JoinAnd1Split', color: '#80ff80', level: 2}
 ,{id: 'e5bb55a8-fe1c-4c9d-b744-935c3170f85a', label: 'F_40', type: 'function', group: 'EPC_3JoinAnd1Split', color: '#80ff80', level: 3}
 ,{id: 'd328201e-a28c-4443-95a1-12a2c183307c', label: 'F_30', type: 'function', group: 'EPC_3JoinAnd1Split', color: '#80ff80', level: 4}
 ,{id: 'f8a1a1a1-bb5f-49ea-919c-ef058d71bfcc', label: 'F_20', type: 'function', group: 'EPC_3JoinAnd1Split', color: '#80ff80', level: 5}
 ,{id: '9ec372b6-5458-452d-8e98-25dded668b3b', label: 'F_10', type: 'function', group: 'EPC_3JoinAnd1Split', color: '#80ff80', level: 6}
 ,{id: 'd16f234a-af7c-4a33-9464-5d2f7f1902a0', label: 'or', type: 'operator', group: 'EPC_3JoinAnd1Split', color: 'gray', level: 7}
 ,{id: '7a564e93-c651-4700-ae37-97b875024183', label: 'xor', type: 'operator', group: 'EPC_3JoinAnd1Split', color: 'gray', level: 8}
 ,{id: '0534d417-d189-4943-8c3e-c10f5ecf1397', label: 'F_11', type: 'function', group: 'EPC_3JoinAnd1Split', color: '#80ff80', level: 9}
 ,{id: '3e6970ad-e510-42a8-9f9f-c782b17e97a6', label: 'F_6', type: 'function', group: 'EPC_3JoinAnd1Split', color: '#80ff80', level: 1}
 ,{id: '71a16686-5bc5-4f25-bdad-b0485ec9794f', label: 'xor', type: 'operator', group: 'EPC_3JoinAnd1Split', color: 'gray', level: 2}
 ,{id: 'd64877a1-cd8a-4ba5-b39a-69ba8dde0e80', label: 'F_1', type: 'function', group: 'EPC_3JoinAnd1Split', color: '#80ff80', level: 1}
 ,{id: 'a51d9fb4-d4ef-4c2f-93cc-73f058f5f44a', label: 'F_2', type: 'function', group: 'EPC_3JoinAnd1Split', color: '#80ff80', level: 2}
 ,{id: '8bad378c-350b-4419-a26a-04c718d1e3e0', label: 'and', type: 'operator', group: 'EPC_3JoinAnd1Split', color: 'gray', level: 3}
 ,{id: '5af0597b-1763-428f-b38d-f7e12405368d', label: 'F_4', type: 'function', group: 'EPC_3JoinAnd1Split', color: '#80ff80', level: 4}
 ,{id: '8c90e1b1-cd98-48fc-98c2-4f1680c233dc', label: 'F_5', type: 'function', group: 'EPC_3JoinAnd1Split', color: '#80ff80', level: 4}
 ,{id: 'a331c10a-97ce-473b-b199-b0921cd332a1', label: 'F_3', type: 'function', group: 'EPC_3JoinAnd1Split', color: '#80ff80', level: 4}
 ,{id: '3e95e316-8a30-42a0-a5ff-9cf899e656d7', label: 'F_7', type: 'function', group: 'EPC_3JoinAnd1Split', color: '#80ff80', level: 1}
 ,{id: 'd9a4033f-cfc6-4991-9906-648165d9fb7d', label: 'F_9', type: 'function', group: 'EPC_3JoinAnd1Split', color: '#80ff80', level: 1}
 ,{id: 'f5f84f91-949f-434c-9094-117a62b63177', label: 'F_8', type: 'function', group: 'EPC_3JoinAnd1Split', color: '#80ff80', level: 1}
 ]; edges = [{from: 'de10d570-be9b-4b46-83c7-ac82c43fbea8', to: 'c26ec8db-fa88-4b21-9c32-cb882d953f25', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: 'd328201e-a28c-4443-95a1-12a2c183307c', to: 'f8a1a1a1-bb5f-49ea-919c-ef058d71bfcc', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: 'e5bb55a8-fe1c-4c9d-b744-935c3170f85a', to: 'd328201e-a28c-4443-95a1-12a2c183307c', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: '3e6970ad-e510-42a8-9f9f-c782b17e97a6', to: '71a16686-5bc5-4f25-bdad-b0485ec9794f', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: 'd64877a1-cd8a-4ba5-b39a-69ba8dde0e80', to: 'a51d9fb4-d4ef-4c2f-93cc-73f058f5f44a', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: '3e95e316-8a30-42a0-a5ff-9cf899e656d7', to: '71a16686-5bc5-4f25-bdad-b0485ec9794f', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: 'd9a4033f-cfc6-4991-9906-648165d9fb7d', to: '71a16686-5bc5-4f25-bdad-b0485ec9794f', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: 'c26ec8db-fa88-4b21-9c32-cb882d953f25', to: 'e5bb55a8-fe1c-4c9d-b744-935c3170f85a', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: '71a16686-5bc5-4f25-bdad-b0485ec9794f', to: 'd16f234a-af7c-4a33-9464-5d2f7f1902a0', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: 'd16f234a-af7c-4a33-9464-5d2f7f1902a0', to: '7a564e93-c651-4700-ae37-97b875024183', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: 'a51d9fb4-d4ef-4c2f-93cc-73f058f5f44a', to: '8bad378c-350b-4419-a26a-04c718d1e3e0', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: 'f5f84f91-949f-434c-9094-117a62b63177', to: '71a16686-5bc5-4f25-bdad-b0485ec9794f', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: 'f8a1a1a1-bb5f-49ea-919c-ef058d71bfcc', to: '9ec372b6-5458-452d-8e98-25dded668b3b', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: '7a564e93-c651-4700-ae37-97b875024183', to: '0534d417-d189-4943-8c3e-c10f5ecf1397', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: '9ec372b6-5458-452d-8e98-25dded668b3b', to: 'd16f234a-af7c-4a33-9464-5d2f7f1902a0', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: '8bad378c-350b-4419-a26a-04c718d1e3e0', to: '5af0597b-1763-428f-b38d-f7e12405368d', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: '8bad378c-350b-4419-a26a-04c718d1e3e0', to: '8c90e1b1-cd98-48fc-98c2-4f1680c233dc', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: '8bad378c-350b-4419-a26a-04c718d1e3e0', to: 'a331c10a-97ce-473b-b199-b0921cd332a1', arrows:'to', style: 'arrow', color: 'gray'}
 ,{from: '5af0597b-1763-428f-b38d-f7e12405368d', to: '7a564e93-c651-4700-ae37-97b875024183', arrows:'to', style: 'arrow', color: 'gray'}
 ];
		 var satelliteObjects = [];
		 var relations = [];


		 var musterErgebnis = "node xor: (0, 0)node F_11: (0, 60)node F_1: (150, -240)node F_2: (150, -180)node and: (150, -120)node F_4: (50, -60)node F_5: (150, -60)node F_3: (250, -60)node or: (-100, -60)node F_60: (-50, -420)node F_50: (-50, -360)node F_40: (-50, -300)node F_30: (-50, -240)node F_20: (-50, -180)node F_10: (-50, -120)node xor: (-300, -120)node F_9: (-250, -180)node F_7: (-350, -180)node F_6: (-450, -180)node F_8: (-150, -180)F_60 -> F_50: [(-50, -395)(-50, -385)]F_30 -> F_20: [(-50, -215)(-50, -205)]F_40 -> F_30: [(-50, -275)(-50, -265)]F_6 -> xor: [(-450, -155)(-450, -120)(-325, -120)]F_1 -> F_2: [(150, -215)(150, -205)]F_7 -> xor: [(-350, -155)(-350, -120)(-325, -120)]F_9 -> xor: [(-250, -155)(-250, -120)(-275, -120)]F_50 -> F_40: [(-50, -335)(-50, -325)]xor -> or: [(-300, -95)(-300, -60)(-125, -60)]or -> xor: [(-100, -35)(-100, 0)(-25, 0)]F_2 -> and: [(150, -155)(150, -145)]F_8 -> xor: [(-150, -155)(-150, -120)(-275, -120)]F_20 -> F_10: [(-50, -155)(-50, -145)]xor -> F_11: [(0, 25)(0, 35)]F_10 -> or: [(-50, -95)(-50, -60)(-75, -60)]and -> F_4: [(125, -120)(50, -120)(50, -85)]and -> F_5: [(150, -95)(150, -85)]and -> F_3: [(175, -120)(250, -120)(250, -85)]F_4 -> xor: [(50, -35)(50, 0)(25, 0)]";



         expect(setNodesAndEdges("test", undefined, nodes, edges, satelliteObjects, relations, 0, 10, 0, 0, true)).toEqual(musterErgebnis);
     });
	
		it("EPC_2JoinsAnd1Split [100, 50, 0, 0]", function () {
		
		var nodes = [{id: 'e275d24d-95fa-4718-be4f-266e201ec28d', label: 'F_3', type: 'function', group: 'EPC_2JoinsAnd1Split', color: '#80ff80', level: 1}
,{id: '3695ea0b-748b-4319-9ae0-a94ffc0669f0', label: 'xor', type: 'operator', group: 'EPC_2JoinsAnd1Split', color: 'gray', level: 2}
,{id: 'fed1a4af-ac09-4215-a793-1ae0d13592c4', label: 'F_4', type: 'function', group: 'EPC_2JoinsAnd1Split', color: '#80ff80', level: 3}
,{id: 'f898b782-cac5-4919-a63d-1437e599fe41', label: 'xor', type: 'operator', group: 'EPC_2JoinsAnd1Split', color: 'gray', level: 4}
,{id: 'e06e9505-8bd4-4a8f-9616-991b281eacef', label: 'xor', type: 'operator', group: 'EPC_2JoinsAnd1Split', color: 'gray', level: 5}
,{id: '185d6751-b44d-493a-932d-412be72fae96', label: 'F_5', type: 'function', group: 'EPC_2JoinsAnd1Split', color: '#80ff80', level: 5}
,{id: 'f689231e-a2d9-4a8b-96c8-56c558952c76', label: 'F_2', type: 'function', group: 'EPC_2JoinsAnd1Split', color: '#80ff80', level: 1}
,{id: '348e7bfe-0f20-40be-88a6-0b816f7f857a', label: 'F_1', type: 'function', group: 'EPC_2JoinsAnd1Split', color: '#80ff80', level: 1}
]; edges = [{from: 'e275d24d-95fa-4718-be4f-266e201ec28d', to: '3695ea0b-748b-4319-9ae0-a94ffc0669f0', arrows:'to', style: 'arrow', color: 'gray'}
,{from: 'f689231e-a2d9-4a8b-96c8-56c558952c76', to: '3695ea0b-748b-4319-9ae0-a94ffc0669f0', arrows:'to', style: 'arrow', color: 'gray'}
,{from: 'e06e9505-8bd4-4a8f-9616-991b281eacef', to: '3695ea0b-748b-4319-9ae0-a94ffc0669f0', arrows:'to', style: 'arrow', color: 'gray'}
,{from: 'f898b782-cac5-4919-a63d-1437e599fe41', to: 'e06e9505-8bd4-4a8f-9616-991b281eacef', arrows:'to', style: 'arrow', color: 'gray'}
,{from: 'f898b782-cac5-4919-a63d-1437e599fe41', to: '185d6751-b44d-493a-932d-412be72fae96', arrows:'to', style: 'arrow', color: 'gray'}
,{from: 'fed1a4af-ac09-4215-a793-1ae0d13592c4', to: 'f898b782-cac5-4919-a63d-1437e599fe41', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '348e7bfe-0f20-40be-88a6-0b816f7f857a', to: 'e06e9505-8bd4-4a8f-9616-991b281eacef', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '3695ea0b-748b-4319-9ae0-a94ffc0669f0', to: 'fed1a4af-ac09-4215-a793-1ae0d13592c4', arrows:'to', style: 'arrow', color: 'gray'}
];
		var satelliteObjects = [];
		var relations = [];


		var musterErgebnis = "node xor: (0, 0)node F_4: (0, 100)node xor: (0, 200)node F_5: (0, 300)node F_2: (0, -100)node F_3: (-200, -100)node xor: (200, -100)node F_1: (200, -200)F_3 -> xor: [(-200, -75)(-200, 0)(-25, 0)]F_2 -> xor: [(0, -75)(0, -25)]xor -> xor: [(200, -75)(200, 0)(25, 0)]xor -> F_5: [(0, 225)(0, 275)]F_4 -> xor: [(0, 125)(0, 175)]F_1 -> xor: [(200, -175)(200, -125)]xor -> F_4: [(0, 25)(0, 75)]xor -> xor: [(25, 200)(325, 200)(325, -100)(225, -100)]";



        expect(setNodesAndEdges("test", undefined, nodes, edges, satelliteObjects, relations, 100, 50, 0, 0, true)).toEqual(musterErgebnis);
    });
	
	it("EPC_2JoinsAnd1Split [0, 10, 0, 0]", function () {
		
		var nodes = [{id: 'e275d24d-95fa-4718-be4f-266e201ec28d', label: 'F_3', type: 'function', group: 'EPC_2JoinsAnd1Split', color: '#80ff80', level: 1}
,{id: '3695ea0b-748b-4319-9ae0-a94ffc0669f0', label: 'xor', type: 'operator', group: 'EPC_2JoinsAnd1Split', color: 'gray', level: 2}
,{id: 'fed1a4af-ac09-4215-a793-1ae0d13592c4', label: 'F_4', type: 'function', group: 'EPC_2JoinsAnd1Split', color: '#80ff80', level: 3}
,{id: 'f898b782-cac5-4919-a63d-1437e599fe41', label: 'xor', type: 'operator', group: 'EPC_2JoinsAnd1Split', color: 'gray', level: 4}
,{id: 'e06e9505-8bd4-4a8f-9616-991b281eacef', label: 'xor', type: 'operator', group: 'EPC_2JoinsAnd1Split', color: 'gray', level: 5}
,{id: '185d6751-b44d-493a-932d-412be72fae96', label: 'F_5', type: 'function', group: 'EPC_2JoinsAnd1Split', color: '#80ff80', level: 5}
,{id: 'f689231e-a2d9-4a8b-96c8-56c558952c76', label: 'F_2', type: 'function', group: 'EPC_2JoinsAnd1Split', color: '#80ff80', level: 1}
,{id: '348e7bfe-0f20-40be-88a6-0b816f7f857a', label: 'F_1', type: 'function', group: 'EPC_2JoinsAnd1Split', color: '#80ff80', level: 1}
]; edges = [{from: 'e275d24d-95fa-4718-be4f-266e201ec28d', to: '3695ea0b-748b-4319-9ae0-a94ffc0669f0', arrows:'to', style: 'arrow', color: 'gray'}
,{from: 'f689231e-a2d9-4a8b-96c8-56c558952c76', to: '3695ea0b-748b-4319-9ae0-a94ffc0669f0', arrows:'to', style: 'arrow', color: 'gray'}
,{from: 'e06e9505-8bd4-4a8f-9616-991b281eacef', to: '3695ea0b-748b-4319-9ae0-a94ffc0669f0', arrows:'to', style: 'arrow', color: 'gray'}
,{from: 'f898b782-cac5-4919-a63d-1437e599fe41', to: 'e06e9505-8bd4-4a8f-9616-991b281eacef', arrows:'to', style: 'arrow', color: 'gray'}
,{from: 'f898b782-cac5-4919-a63d-1437e599fe41', to: '185d6751-b44d-493a-932d-412be72fae96', arrows:'to', style: 'arrow', color: 'gray'}
,{from: 'fed1a4af-ac09-4215-a793-1ae0d13592c4', to: 'f898b782-cac5-4919-a63d-1437e599fe41', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '348e7bfe-0f20-40be-88a6-0b816f7f857a', to: 'e06e9505-8bd4-4a8f-9616-991b281eacef', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '3695ea0b-748b-4319-9ae0-a94ffc0669f0', to: 'fed1a4af-ac09-4215-a793-1ae0d13592c4', arrows:'to', style: 'arrow', color: 'gray'}
];
		var satelliteObjects = [];
		var relations = [];


		var musterErgebnis = "node xor: (0, 0)node F_4: (0, 60)node xor: (0, 120)node F_5: (0, 180)node F_2: (0, -60)node F_3: (-100, -60)node xor: (100, -60)node F_1: (100, -120)F_3 -> xor: [(-100, -35)(-100, 0)(-25, 0)]F_2 -> xor: [(0, -35)(0, -25)]xor -> xor: [(100, -35)(100, 0)(25, 0)]xor -> F_5: [(0, 145)(0, 155)]F_4 -> xor: [(0, 85)(0, 95)]F_1 -> xor: [(100, -95)(100, -85)]xor -> F_4: [(0, 25)(0, 35)]xor -> xor: [(25, 120)(125, 120)(125, -60)(125, -60)]";



        expect(setNodesAndEdges("test", undefined, nodes, edges, satelliteObjects, relations, 0, 10, 0, 0, true)).toEqual(musterErgebnis);
    });
	
		it("simple relation 1 element [100, 50, 40, 20]", function () {
		
		var nodes = [{id: '1', label: 'E1', type: 'event', group: 'EPC', color: '#FF8080', level: 1}
,{id: '3', label: 'F1', type: 'function', group: 'EPC', color: '#80ff80', level: 2}
,{id: '2', label: 'E2', type: 'event', group: 'EPC', color: '#FF8080', level: 3}
,{id: '8', label: 'F2', type: 'function', group: 'EPC', color: '#80ff80', level: 4}
,{id: '9', label: 'E3', type: 'event', group: 'EPC', color: '#FF8080', level: 5}
]; edges = [{from: '1', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '3', to: '2', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '2', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '8', to: '9', arrows:'to', style: 'arrow', color: 'gray'}
]; satelliteObjects = [{id: '6', label: 'R', type: 'orgUnit', color: '#FFFF80'}
,{id: '10', label: 'R1', type: 'orgUnit', color: '#FFFF80'}
]; relations = [{from: '6', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '10', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
];


		var musterErgebnis = "node E1: (0, 0)node F1: (0, 100)assignedObject R: (140, 100)node E2: (0, 200)node F2: (0, 300)assignedObject R1: (140, 300)node E3: (0, 400)F1 -> R: [(50, 100)(90, 100)]F2 -> R1: [(50, 300)(90, 300)]E1 -> F1: [(0, 25)(0, 75)]F1 -> E2: [(0, 125)(0, 175)]E2 -> F2: [(0, 225)(0, 275)]F2 -> E3: [(0, 325)(0, 375)]";



        expect(setNodesAndEdges("test", undefined, nodes, edges, satelliteObjects, relations, 100, 50, 40, 20, true)).toEqual(musterErgebnis);
    });
	
			it("simple relation 1 element [100, 50, 10, 0]", function () {
		
		var nodes = [{id: '1', label: 'E1', type: 'event', group: 'EPC', color: '#FF8080', level: 1}
,{id: '3', label: 'F1', type: 'function', group: 'EPC', color: '#80ff80', level: 2}
,{id: '2', label: 'E2', type: 'event', group: 'EPC', color: '#FF8080', level: 3}
,{id: '8', label: 'F2', type: 'function', group: 'EPC', color: '#80ff80', level: 4}
,{id: '9', label: 'E3', type: 'event', group: 'EPC', color: '#FF8080', level: 5}
]; edges = [{from: '1', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '3', to: '2', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '2', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '8', to: '9', arrows:'to', style: 'arrow', color: 'gray'}
]; satelliteObjects = [{id: '6', label: 'R', type: 'orgUnit', color: '#FFFF80'}
,{id: '10', label: 'R1', type: 'orgUnit', color: '#FFFF80'}
]; relations = [{from: '6', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '10', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
];


		var musterErgebnis = "node E1: (0, 0)node F1: (0, 100)assignedObject R: (110, 100)node E2: (0, 200)node F2: (0, 300)assignedObject R1: (110, 300)node E3: (0, 400)F1 -> R: [(50, 100)(60, 100)]F2 -> R1: [(50, 300)(60, 300)]E1 -> F1: [(0, 25)(0, 75)]F1 -> E2: [(0, 125)(0, 175)]E2 -> F2: [(0, 225)(0, 275)]F2 -> E3: [(0, 325)(0, 375)]";



        expect(setNodesAndEdges("test", undefined, nodes, edges, satelliteObjects, relations, 100, 50, 10, 0, true)).toEqual(musterErgebnis);
    });
	
			it("simple relation 2 elements [100, 50, 40, 20]", function () {
		
		var nodes = [{id: '1', label: 'E1', type: 'event', group: 'EPC', color: '#FF8080', level: 1}
,{id: '3', label: 'F1', type: 'function', group: 'EPC', color: '#80ff80', level: 2}
,{id: '2', label: 'E2', type: 'event', group: 'EPC', color: '#FF8080', level: 3}
,{id: '8', label: 'F2', type: 'function', group: 'EPC', color: '#80ff80', level: 4}
,{id: '9', label: 'E3', type: 'event', group: 'EPC', color: '#FF8080', level: 5}
]; edges = [{from: '1', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '3', to: '2', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '2', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '8', to: '9', arrows:'to', style: 'arrow', color: 'gray'}
]; satelliteObjects = [{id: '6', label: 'R', type: 'orgUnit', color: '#FFFF80'}
,{id: '10', label: 'R1', type: 'orgUnit', color: '#FFFF80'}
,{id: '200', label: 'R2', type: 'orgUnit', color: '#FFFF80'}
]; relations = [{from: '6', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '200', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '10', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
];


		var musterErgebnis = "node E1: (0, 0)node F1: (0, 100)assignedObject R: (140, 100)node E2: (0, 200)node F2: (0, 300)assignedObject R2: (140, 335)assignedObject R1: (140, 265)node E3: (0, 400)F1 -> R: [(50, 100)(90, 100)]F2 -> R2: [(50, 300)(60, 300)(60, 335)(90, 335)]F2 -> R1: [(50, 300)(60, 300)(60, 265)(90, 265)]E1 -> F1: [(0, 25)(0, 75)]F1 -> E2: [(0, 125)(0, 175)]E2 -> F2: [(0, 225)(0, 275)]F2 -> E3: [(0, 325)(0, 375)]";



        expect(setNodesAndEdges("test", undefined, nodes, edges, satelliteObjects, relations, 100, 50, 40, 20, true)).toEqual(musterErgebnis);
    });
	
			it("simple relation 2 elements [100, 50, 10, 0]", function () {
		
		var nodes = [{id: '1', label: 'E1', type: 'event', group: 'EPC', color: '#FF8080', level: 1}
,{id: '3', label: 'F1', type: 'function', group: 'EPC', color: '#80ff80', level: 2}
,{id: '2', label: 'E2', type: 'event', group: 'EPC', color: '#FF8080', level: 3}
,{id: '8', label: 'F2', type: 'function', group: 'EPC', color: '#80ff80', level: 4}
,{id: '9', label: 'E3', type: 'event', group: 'EPC', color: '#FF8080', level: 5}
]; edges = [{from: '1', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '3', to: '2', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '2', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '8', to: '9', arrows:'to', style: 'arrow', color: 'gray'}
]; satelliteObjects = [{id: '6', label: 'R', type: 'orgUnit', color: '#FFFF80'}
,{id: '10', label: 'R1', type: 'orgUnit', color: '#FFFF80'}
,{id: '200', label: 'R2', type: 'orgUnit', color: '#FFFF80'}
]; relations = [{from: '6', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '200', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '10', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
];


		var musterErgebnis = "node E1: (0, 0)node F1: (0, 100)assignedObject R: (110, 100)node E2: (0, 200)node F2: (0, 300)assignedObject R2: (110, 325)assignedObject R1: (110, 275)node E3: (0, 400)F1 -> R: [(50, 100)(60, 100)]F2 -> R2: [(50, 300)(60, 300)(60, 325)(60, 325)]F2 -> R1: [(50, 300)(60, 300)(60, 275)(60, 275)]E1 -> F1: [(0, 25)(0, 75)]F1 -> E2: [(0, 125)(0, 175)]E2 -> F2: [(0, 225)(0, 275)]F2 -> E3: [(0, 325)(0, 375)]";



        expect(setNodesAndEdges("test", undefined, nodes, edges, satelliteObjects, relations, 100, 50, 10, 0, true)).toEqual(musterErgebnis);
    });
	
			it("simple relation 3 elements [100, 50, 40, 20]", function () {
		
		var nodes = [{id: '1', label: 'E1', type: 'event', group: 'EPC', color: '#FF8080', level: 1}
,{id: '3', label: 'F1', type: 'function', group: 'EPC', color: '#80ff80', level: 2}
,{id: '2', label: 'E2', type: 'event', group: 'EPC', color: '#FF8080', level: 3}
,{id: '8', label: 'F2', type: 'function', group: 'EPC', color: '#80ff80', level: 4}
,{id: '9', label: 'E3', type: 'event', group: 'EPC', color: '#FF8080', level: 5}
]; edges = [{from: '1', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '3', to: '2', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '2', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '8', to: '9', arrows:'to', style: 'arrow', color: 'gray'}
]; satelliteObjects = [{id: '6', label: 'R', type: 'orgUnit', color: '#FFFF80'}
,{id: '10', label: 'R1', type: 'orgUnit', color: '#FFFF80'}
,{id: '200', label: 'R2', type: 'orgUnit', color: '#FFFF80'}
,{id: '202', label: 'R3', type: 'orgUnit', color: '#FFFF80'}
]; relations = [{from: '6', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '200', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '202', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '10', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
];


		var musterErgebnis = "node E1: (0, 0)node F1: (0, 100)assignedObject R: (140, 100)node E2: (0, 200)node F2: (0, 300)assignedObject R2: (140, 230)assignedObject R3: (140, 300)assignedObject R1: (140, 370)node E3: (0, 400)F1 -> R: [(50, 100)(90, 100)]F2 -> R2: [(50, 300)(60, 300)(60, 230)(90, 230)]F2 -> R3: [(50, 300)(90, 300)]F2 -> R1: [(50, 300)(60, 300)(60, 370)(90, 370)]E1 -> F1: [(0, 25)(0, 75)]F1 -> E2: [(0, 125)(0, 175)]E2 -> F2: [(0, 225)(0, 275)]F2 -> E3: [(0, 325)(0, 375)]";



        expect(setNodesAndEdges("test", undefined, nodes, edges, satelliteObjects, relations, 100, 50, 40, 20, true)).toEqual(musterErgebnis);
    });
	
			it("simple relation 3 elements [100, 50, 10, 0]", function () {
		
		var nodes = [{id: '1', label: 'E1', type: 'event', group: 'EPC', color: '#FF8080', level: 1}
,{id: '3', label: 'F1', type: 'function', group: 'EPC', color: '#80ff80', level: 2}
,{id: '2', label: 'E2', type: 'event', group: 'EPC', color: '#FF8080', level: 3}
,{id: '8', label: 'F2', type: 'function', group: 'EPC', color: '#80ff80', level: 4}
,{id: '9', label: 'E3', type: 'event', group: 'EPC', color: '#FF8080', level: 5}
]; edges = [{from: '1', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '3', to: '2', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '2', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '8', to: '9', arrows:'to', style: 'arrow', color: 'gray'}
]; satelliteObjects = [{id: '6', label: 'R', type: 'orgUnit', color: '#FFFF80'}
,{id: '10', label: 'R1', type: 'orgUnit', color: '#FFFF80'}
,{id: '200', label: 'R2', type: 'orgUnit', color: '#FFFF80'}
,{id: '202', label: 'R3', type: 'orgUnit', color: '#FFFF80'}
]; relations = [{from: '6', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '200', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '202', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '10', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
];


		var musterErgebnis = "node E1: (0, 0)node F1: (0, 100)assignedObject R: (110, 100)node E2: (0, 200)node F2: (0, 300)assignedObject R2: (110, 250)assignedObject R3: (110, 300)assignedObject R1: (110, 350)node E3: (0, 400)F1 -> R: [(50, 100)(60, 100)]F2 -> R2: [(50, 300)(60, 300)(60, 250)(60, 250)]F2 -> R3: [(50, 300)(60, 300)]F2 -> R1: [(50, 300)(60, 300)(60, 350)(60, 350)]E1 -> F1: [(0, 25)(0, 75)]F1 -> E2: [(0, 125)(0, 175)]E2 -> F2: [(0, 225)(0, 275)]F2 -> E3: [(0, 325)(0, 375)]";



        expect(setNodesAndEdges("test", undefined, nodes, edges, satelliteObjects, relations, 100, 50, 10, 0, true)).toEqual(musterErgebnis);
    });
	
			it("simple relation 4 elements [100, 50, 40, 20]", function () {
		
		var nodes = [{id: '1', label: 'E1', type: 'event', group: 'EPC', color: '#FF8080', level: 1}
,{id: '3', label: 'F1', type: 'function', group: 'EPC', color: '#80ff80', level: 2}
,{id: '2', label: 'E2', type: 'event', group: 'EPC', color: '#FF8080', level: 3}
,{id: '8', label: 'F2', type: 'function', group: 'EPC', color: '#80ff80', level: 4}
,{id: '9', label: 'E3', type: 'event', group: 'EPC', color: '#FF8080', level: 5}
]; edges = [{from: '1', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '3', to: '2', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '2', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '8', to: '9', arrows:'to', style: 'arrow', color: 'gray'}
]; satelliteObjects = [{id: '6', label: 'R', type: 'orgUnit', color: '#FFFF80'}
,{id: '10', label: 'R1', type: 'orgUnit', color: '#FFFF80'}
,{id: '200', label: 'R2', type: 'orgUnit', color: '#FFFF80'}
,{id: '202', label: 'R3', type: 'orgUnit', color: '#FFFF80'}
,{id: '204', label: 'R4', type: 'orgUnit', color: '#FFFF80'}
]; relations = [{from: '6', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '200', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '202', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '204', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '10', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
];


		var musterErgebnis = "node E1: (0, 0)node F1: (0, 100)assignedObject R: (140, 100)node E2: (0, 200)node F2: (0, 300)assignedObject R2: (140, 195)assignedObject R3: (140, 335)assignedObject R4: (140, 265)assignedObject R1: (140, 405)node E3: (0, 400)F1 -> R: [(50, 100)(90, 100)]F2 -> R2: [(50, 300)(60, 300)(60, 195)(90, 195)]F2 -> R3: [(50, 300)(60, 300)(60, 335)(90, 335)]F2 -> R4: [(50, 300)(60, 300)(60, 265)(90, 265)]F2 -> R1: [(50, 300)(60, 300)(60, 405)(90, 405)]E1 -> F1: [(0, 25)(0, 75)]F1 -> E2: [(0, 125)(0, 175)]E2 -> F2: [(0, 225)(0, 275)]F2 -> E3: [(0, 325)(0, 375)]";



        expect(setNodesAndEdges("test", undefined, nodes, edges, satelliteObjects, relations, 100, 50, 40, 20, true)).toEqual(musterErgebnis);
    });
	
			it("simple relation 4 elements [100, 50, 10, 0]", function () {
		
		var nodes = [{id: '1', label: 'E1', type: 'event', group: 'EPC', color: '#FF8080', level: 1}
,{id: '3', label: 'F1', type: 'function', group: 'EPC', color: '#80ff80', level: 2}
,{id: '2', label: 'E2', type: 'event', group: 'EPC', color: '#FF8080', level: 3}
,{id: '8', label: 'F2', type: 'function', group: 'EPC', color: '#80ff80', level: 4}
,{id: '9', label: 'E3', type: 'event', group: 'EPC', color: '#FF8080', level: 5}
]; edges = [{from: '1', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '3', to: '2', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '2', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '8', to: '9', arrows:'to', style: 'arrow', color: 'gray'}
]; satelliteObjects = [{id: '6', label: 'R', type: 'orgUnit', color: '#FFFF80'}
,{id: '10', label: 'R1', type: 'orgUnit', color: '#FFFF80'}
,{id: '200', label: 'R2', type: 'orgUnit', color: '#FFFF80'}
,{id: '202', label: 'R3', type: 'orgUnit', color: '#FFFF80'}
,{id: '204', label: 'R4', type: 'orgUnit', color: '#FFFF80'}
]; relations = [{from: '6', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '200', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '202', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '204', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '10', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
];


		var musterErgebnis = "node E1: (0, 0)node F1: (0, 100)assignedObject R: (110, 100)node E2: (0, 200)node F2: (0, 300)assignedObject R2: (110, 225)assignedObject R3: (110, 325)assignedObject R4: (110, 275)assignedObject R1: (110, 375)node E3: (0, 400)F1 -> R: [(50, 100)(60, 100)]F2 -> R2: [(50, 300)(60, 300)(60, 225)(60, 225)]F2 -> R3: [(50, 300)(60, 300)(60, 325)(60, 325)]F2 -> R4: [(50, 300)(60, 300)(60, 275)(60, 275)]F2 -> R1: [(50, 300)(60, 300)(60, 375)(60, 375)]E1 -> F1: [(0, 25)(0, 75)]F1 -> E2: [(0, 125)(0, 175)]E2 -> F2: [(0, 225)(0, 275)]F2 -> E3: [(0, 325)(0, 375)]";



        expect(setNodesAndEdges("test", undefined, nodes, edges, satelliteObjects, relations, 100, 50, 10, 0, true)).toEqual(musterErgebnis);
    });
	
			it("simple relation 5 elements [100, 50, 40, 20]", function () {
		
		var nodes = [{id: '1', label: 'E1', type: 'event', group: 'EPC', color: '#FF8080', level: 1}
,{id: '3', label: 'F1', type: 'function', group: 'EPC', color: '#80ff80', level: 2}
,{id: '2', label: 'E2', type: 'event', group: 'EPC', color: '#FF8080', level: 3}
,{id: '8', label: 'F2', type: 'function', group: 'EPC', color: '#80ff80', level: 4}
,{id: '9', label: 'E3', type: 'event', group: 'EPC', color: '#FF8080', level: 5}
]; edges = [{from: '1', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '3', to: '2', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '2', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '8', to: '9', arrows:'to', style: 'arrow', color: 'gray'}
]; satelliteObjects = [{id: '6', label: 'R', type: 'orgUnit', color: '#FFFF80'}
,{id: '10', label: 'R1', type: 'orgUnit', color: '#FFFF80'}
,{id: '200', label: 'R2', type: 'orgUnit', color: '#FFFF80'}
,{id: '202', label: 'R3', type: 'orgUnit', color: '#FFFF80'}
,{id: '204', label: 'R4', type: 'orgUnit', color: '#FFFF80'}
,{id: '206', label: 'R5', type: 'orgUnit', color: '#FFFF80'}
]; relations = [{from: '6', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '200', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '202', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '204', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '206', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '10', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
];


		var musterErgebnis = "node E1: (0, 0)node F1: (0, 100)assignedObject R: (140, 100)node E2: (0, 200)node F2: (0, 300)assignedObject R2: (140, 160)assignedObject R3: (140, 230)assignedObject R4: (140, 300)assignedObject R5: (140, 370)assignedObject R1: (140, 440)node E3: (0, 400)F1 -> R: [(50, 100)(90, 100)]F2 -> R2: [(50, 300)(60, 300)(60, 160)(90, 160)]F2 -> R3: [(50, 300)(60, 300)(60, 230)(90, 230)]F2 -> R4: [(50, 300)(90, 300)]F2 -> R5: [(50, 300)(60, 300)(60, 370)(90, 370)]F2 -> R1: [(50, 300)(60, 300)(60, 440)(90, 440)]E1 -> F1: [(0, 25)(0, 75)]F1 -> E2: [(0, 125)(0, 175)]E2 -> F2: [(0, 225)(0, 275)]F2 -> E3: [(0, 325)(0, 375)]";



        expect(setNodesAndEdges("test", undefined, nodes, edges, satelliteObjects, relations, 100, 50, 40, 20, true)).toEqual(musterErgebnis);
    });
	
			it("simple relation 5 elements [100, 50, 10, 0]", function () {
		
		var nodes = [{id: '1', label: 'E1', type: 'event', group: 'EPC', color: '#FF8080', level: 1}
,{id: '3', label: 'F1', type: 'function', group: 'EPC', color: '#80ff80', level: 2}
,{id: '2', label: 'E2', type: 'event', group: 'EPC', color: '#FF8080', level: 3}
,{id: '8', label: 'F2', type: 'function', group: 'EPC', color: '#80ff80', level: 4}
,{id: '9', label: 'E3', type: 'event', group: 'EPC', color: '#FF8080', level: 5}
]; edges = [{from: '1', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '3', to: '2', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '2', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '8', to: '9', arrows:'to', style: 'arrow', color: 'gray'}
]; satelliteObjects = [{id: '6', label: 'R', type: 'orgUnit', color: '#FFFF80'}
,{id: '10', label: 'R1', type: 'orgUnit', color: '#FFFF80'}
,{id: '200', label: 'R2', type: 'orgUnit', color: '#FFFF80'}
,{id: '202', label: 'R3', type: 'orgUnit', color: '#FFFF80'}
,{id: '204', label: 'R4', type: 'orgUnit', color: '#FFFF80'}
,{id: '206', label: 'R5', type: 'orgUnit', color: '#FFFF80'}
]; relations = [{from: '6', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '200', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '202', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '204', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '206', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '10', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
];


		var musterErgebnis = "node E1: (0, 0)node F1: (0, 100)assignedObject R: (110, 100)node E2: (0, 200)node F2: (0, 300)assignedObject R2: (110, 200)assignedObject R3: (110, 250)assignedObject R4: (110, 300)assignedObject R5: (110, 350)assignedObject R1: (110, 400)node E3: (0, 400)F1 -> R: [(50, 100)(60, 100)]F2 -> R2: [(50, 300)(60, 300)(60, 200)(60, 200)]F2 -> R3: [(50, 300)(60, 300)(60, 250)(60, 250)]F2 -> R4: [(50, 300)(60, 300)]F2 -> R5: [(50, 300)(60, 300)(60, 350)(60, 350)]F2 -> R1: [(50, 300)(60, 300)(60, 400)(60, 400)]E1 -> F1: [(0, 25)(0, 75)]F1 -> E2: [(0, 125)(0, 175)]E2 -> F2: [(0, 225)(0, 275)]F2 -> E3: [(0, 325)(0, 375)]";



        expect(setNodesAndEdges("test", undefined, nodes, edges, satelliteObjects, relations, 100, 50, 10, 0, true)).toEqual(musterErgebnis);
    });
	
				it("simple relation 5 elements, 5 elements and join [100, 50, 40, 20]", function () {
		
		var nodes = [{id: '1', label: 'F1', type: 'function', group: 'test', color: '#80ff80', level: 1}
,{id: '2', label: 'F2', type: 'function', group: 'test', color: '#80ff80', level: 2}
,{id: '3', label: 'F3', type: 'function', group: 'test', color: '#80ff80', level: 3}
,{id: '4', label: 'xor', type: 'operator', group: 'test', color: 'gray', level: 4}
,{id: '6', label: 'F5', type: 'function', group: 'test', color: '#80ff80', level: 5}
,{id: '5', label: 'F4', type: 'function', group: 'test', color: '#80ff80', level: 1}
]; edges = [{from: '1', to: '2', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '2', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '3', to: '4', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '5', to: '4', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '4', to: '6', arrows:'to', style: 'arrow', color: 'gray'}
]; satelliteObjects = [{id: '7', label: 'R2', type: 'orgUnit', color: '#FFFF80'}
,{id: '8', label: 'R3', type: 'orgUnit', color: '#FFFF80'}
,{id: '9', label: 'R4', type: 'orgUnit', color: '#FFFF80'}
,{id: '10', label: 'R5', type: 'orgUnit', color: '#FFFF80'}
,{id: '11', label: 'R1', type: 'orgUnit', color: '#FFFF80'}
,{id: '12', label: 'R6', type: 'orgUnit', color: '#FFFF80'}
,{id: '13', label: 'R7', type: 'orgUnit', color: '#FFFF80'}
,{id: '14', label: 'R8', type: 'orgUnit', color: '#FFFF80'}
,{id: '15', label: 'R9', type: 'orgUnit', color: '#FFFF80'}
,{id: '16', label: 'R10', type: 'orgUnit', color: '#FFFF80'}
]; relations = [{from: '11', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '7', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '8', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '9', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '10', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '12', to: '5', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '13', to: '5', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '14', to: '5', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '15', to: '5', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '16', to: '5', arrows:'to', style: 'arrow', color: 'gray'}
];


		var musterErgebnis = "node xor: (0, 0)node F5: (0, 100)node F4: (170, -240)assignedObject R6: (310, -380)assignedObject R7: (310, -310)assignedObject R8: (310, -240)assignedObject R9: (310, -170)assignedObject R10: (310, -100)node F1: (-170, -440)node F2: (-170, -340)node F3: (-170, -240)assignedObject R1: (-30, -380)assignedObject R2: (-30, -310)assignedObject R3: (-30, -240)assignedObject R4: (-30, -170)assignedObject R5: (-30, -100)F3 -> R1: [(-120, -240)(-110, -240)(-110, -380)(-80, -380)]F3 -> R2: [(-120, -240)(-110, -240)(-110, -310)(-80, -310)]F3 -> R3: [(-120, -240)(-80, -240)]F3 -> R4: [(-120, -240)(-110, -240)(-110, -170)(-80, -170)]F3 -> R5: [(-120, -240)(-110, -240)(-110, -100)(-80, -100)]F4 -> R6: [(220, -240)(230, -240)(230, -380)(260, -380)]F4 -> R7: [(220, -240)(230, -240)(230, -310)(260, -310)]F4 -> R8: [(220, -240)(260, -240)]F4 -> R9: [(220, -240)(230, -240)(230, -170)(260, -170)]F4 -> R10: [(220, -240)(230, -240)(230, -100)(260, -100)]F1 -> F2: [(-170, -415)(-170, -365)]F2 -> F3: [(-170, -315)(-170, -265)]F3 -> xor: [(-170, -215)(-170, 0)(-25, 0)]F4 -> xor: [(170, -215)(170, 0)(25, 0)]xor -> F5: [(0, 25)(0, 75)]";



        expect(setNodesAndEdges("test", undefined, nodes, edges, satelliteObjects, relations, 100, 50, 40, 20, true)).toEqual(musterErgebnis);
    });
	
			it("simple relation 5 elements, 5 elements and join [100, 50, 10, 0]", function () {
		
		var nodes = [{id: '1', label: 'F1', type: 'function', group: 'test', color: '#80ff80', level: 1}
,{id: '2', label: 'F2', type: 'function', group: 'test', color: '#80ff80', level: 2}
,{id: '3', label: 'F3', type: 'function', group: 'test', color: '#80ff80', level: 3}
,{id: '4', label: 'xor', type: 'operator', group: 'test', color: 'gray', level: 4}
,{id: '6', label: 'F5', type: 'function', group: 'test', color: '#80ff80', level: 5}
,{id: '5', label: 'F4', type: 'function', group: 'test', color: '#80ff80', level: 1}
]; edges = [{from: '1', to: '2', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '2', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '3', to: '4', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '5', to: '4', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '4', to: '6', arrows:'to', style: 'arrow', color: 'gray'}
]; satelliteObjects = [{id: '7', label: 'R2', type: 'orgUnit', color: '#FFFF80'}
,{id: '8', label: 'R3', type: 'orgUnit', color: '#FFFF80'}
,{id: '9', label: 'R4', type: 'orgUnit', color: '#FFFF80'}
,{id: '10', label: 'R5', type: 'orgUnit', color: '#FFFF80'}
,{id: '11', label: 'R1', type: 'orgUnit', color: '#FFFF80'}
,{id: '12', label: 'R6', type: 'orgUnit', color: '#FFFF80'}
,{id: '13', label: 'R7', type: 'orgUnit', color: '#FFFF80'}
,{id: '14', label: 'R8', type: 'orgUnit', color: '#FFFF80'}
,{id: '15', label: 'R9', type: 'orgUnit', color: '#FFFF80'}
,{id: '16', label: 'R10', type: 'orgUnit', color: '#FFFF80'}
]; relations = [{from: '11', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '7', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '8', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '9', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '10', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '12', to: '5', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '13', to: '5', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '14', to: '5', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '15', to: '5', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '16', to: '5', arrows:'to', style: 'arrow', color: 'gray'}
];


		var musterErgebnis = "node xor: (0, 0)node F5: (0, 100)node F4: (155, -200)assignedObject R6: (265, -300)assignedObject R7: (265, -250)assignedObject R8: (265, -200)assignedObject R9: (265, -150)assignedObject R10: (265, -100)node F1: (-155, -400)node F2: (-155, -300)node F3: (-155, -200)assignedObject R1: (-45, -300)assignedObject R2: (-45, -250)assignedObject R3: (-45, -200)assignedObject R4: (-45, -150)assignedObject R5: (-45, -100)F3 -> R1: [(-105, -200)(-95, -200)(-95, -300)(-95, -300)]F3 -> R2: [(-105, -200)(-95, -200)(-95, -250)(-95, -250)]F3 -> R3: [(-105, -200)(-95, -200)]F3 -> R4: [(-105, -200)(-95, -200)(-95, -150)(-95, -150)]F3 -> R5: [(-105, -200)(-95, -200)(-95, -100)(-95, -100)]F4 -> R6: [(205, -200)(215, -200)(215, -300)(215, -300)]F4 -> R7: [(205, -200)(215, -200)(215, -250)(215, -250)]F4 -> R8: [(205, -200)(215, -200)]F4 -> R9: [(205, -200)(215, -200)(215, -150)(215, -150)]F4 -> R10: [(205, -200)(215, -200)(215, -100)(215, -100)]F1 -> F2: [(-155, -375)(-155, -325)]F2 -> F3: [(-155, -275)(-155, -225)]F3 -> xor: [(-155, -175)(-155, 0)(-25, 0)]F4 -> xor: [(155, -175)(155, 0)(25, 0)]xor -> F5: [(0, 25)(0, 75)]";


        expect(setNodesAndEdges("test", undefined, nodes, edges, satelliteObjects, relations, 100, 50, 10, 0, true)).toEqual(musterErgebnis);
    });
});

describe( "RMMaaS Repository models", function () {



      it("MoHol Solution 1 [100, 50, 0, 0]", function () {
		
		  var nodes = [{id: '8', label: 'Inquiry received', type: 'event', group: 'MoHoL Solution 01', color: '#FF8080', level: 1}
,{id: '9', label: 'Check feasability', type: 'function', group: 'MoHoL Solution 01', color: '#80ff80', level: 2}
,{id: '26', label: 'xor', type: 'operator', group: 'MoHoL Solution 01', color: 'gray', level: 3}
,{id: '11', label: 'Inquiry possibly feasible', type: 'event', group: 'MoHoL Solution 01', color: '#FF8080', level: 4}
,{id: '23', label: 'Bring about clarification', type: 'function', group: 'MoHoL Solution 01', color: '#80ff80', level: 5}
,{id: '22', label: 'xor', type: 'operator', group: 'MoHoL Solution 01', color: 'gray', level: 6}
,{id: '25', label: 'Clarification positive', type: 'event', group: 'MoHoL Solution 01', color: '#FF8080', level: 7}
,{id: '15', label: 'Create offer', type: 'function', group: 'MoHoL Solution 01', color: '#80ff80', level: 8}
,{id: '3', label: 'Offer created', type: 'event', group: 'MoHoL Solution 01', color: '#FF8080', level: 9}
,{id: '18', label: 'Inform Customer', type: 'function', group: 'MoHoL Solution 01', color: '#80ff80', level: 10}
,{id: '21', label: 'Customer informed', type: 'event', group: 'MoHoL Solution 01', color: '#FF8080', level: 11}
,{id: '17', label: 'Clarification negative', type: 'event', group: 'MoHoL Solution 01', color: '#FF8080', level: 7}
,{id: '1', label: 'Create rejection', type: 'function', group: 'MoHoL Solution 01', color: '#80ff80', level: 8}
,{id: '4', label: 'Rejection created', type: 'event', group: 'MoHoL Solution 01', color: '#FF8080', level: 9}
,{id: '13', label: 'Inform Customer', type: 'function', group: 'MoHoL Solution 01', color: '#80ff80', level: 10}
,{id: '7', label: 'Customer informed', type: 'event', group: 'MoHoL Solution 01', color: '#FF8080', level: 11}
,{id: '5', label: 'Inquiry not feasible', type: 'event', group: 'MoHoL Solution 01', color: '#FF8080', level: 4}
,{id: '16', label: 'Create rejection', type: 'function', group: 'MoHoL Solution 01', color: '#80ff80', level: 5}
,{id: '24', label: 'Rejection created', type: 'event', group: 'MoHoL Solution 01', color: '#FF8080', level: 6}
,{id: '20', label: 'Inform customer', type: 'function', group: 'MoHoL Solution 01', color: '#80ff80', level: 7}
,{id: '2', label: 'Customer informed', type: 'event', group: 'MoHoL Solution 01', color: '#FF8080', level: 8}
,{id: '6', label: 'Inquiry feasible', type: 'event', group: 'MoHoL Solution 01', color: '#FF8080', level: 4}
,{id: '10', label: 'Create offer', type: 'function', group: 'MoHoL Solution 01', color: '#80ff80', level: 5}
,{id: '14', label: 'Offer created', type: 'event', group: 'MoHoL Solution 01', color: '#FF8080', level: 6}
,{id: '12', label: 'Inform Customer', type: 'function', group: 'MoHoL Solution 01', color: '#80ff80', level: 7}
,{id: '19', label: 'Customer informed', type: 'event', group: 'MoHoL Solution 01', color: '#FF8080', level: 8}
]; edges = [{from: '1', to: '4', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '3', to: '18', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '4', to: '13', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '5', to: '16', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '6', to: '10', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '8', to: '9', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '9', to: '26', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '10', to: '14', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '11', to: '23', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '12', to: '19', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '13', to: '7', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '14', to: '12', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '15', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '16', to: '24', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '17', to: '1', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '18', to: '21', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '20', to: '2', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '22', to: '25', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '22', to: '17', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '23', to: '22', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '24', to: '20', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '25', to: '15', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '26', to: '11', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '26', to: '5', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '26', to: '6', arrows:'to', style: 'arrow', color: 'gray'}
]; satelliteObjects = []; relations = [];
		
          var musterErgebnis2 = "node Inquiry received: (0, 0)node Check feasability: (0, 100)node xor: (0, 200)node Inquiry possibly feasible: (-300, 300)node Bring about clarification: (-300, 400)node xor: (-300, 500)node Inquiry not feasible: (0, 300)node Create rejection: (0, 400)node Rejection created: (0, 500)node Inform customer: (0, 600)node Customer informed: (0, 700)node Inquiry feasible: (200, 300)node Create offer: (200, 400)node Offer created: (200, 500)node Inform Customer: (200, 600)node Customer informed: (200, 700)node Clarification positive: (-400, 600)node Create offer: (-400, 700)node Offer created: (-400, 800)node Inform Customer: (-400, 900)node Customer informed: (-400, 1000)node Clarification negative: (-200, 600)node Create rejection: (-200, 700)node Rejection created: (-200, 800)node Inform Customer: (-200, 900)node Customer informed: (-200, 1000)Create rejection -> Rejection created: [(-200, 725)(-200, 775)]Offer created -> Inform Customer: [(-400, 825)(-400, 875)]Rejection created -> Inform Customer: [(-200, 825)(-200, 875)]Inquiry not feasible -> Create rejection: [(0, 325)(0, 375)]Inquiry feasible -> Create offer: [(200, 325)(200, 375)]Inquiry received -> Check feasability: [(0, 25)(0, 75)]Check feasability -> xor: [(0, 125)(0, 175)]Create offer -> Offer created: [(200, 425)(200, 475)]Inquiry possibly feasible -> Bring about clarification: [(-300, 325)(-300, 375)]Inform Customer -> Customer informed: [(200, 625)(200, 675)]Inform Customer -> Customer informed: [(-200, 925)(-200, 975)]Offer created -> Inform Customer: [(200, 525)(200, 575)]Create offer -> Offer created: [(-400, 725)(-400, 775)]Create rejection -> Rejection created: [(0, 425)(0, 475)]Clarification negative -> Create rejection: [(-200, 625)(-200, 675)]Inform Customer -> Customer informed: [(-400, 925)(-400, 975)]Inform customer -> Customer informed: [(0, 625)(0, 675)]xor -> Clarification positive: [(-325, 500)(-400, 500)(-400, 575)]xor -> Clarification negative: [(-275, 500)(-200, 500)(-200, 575)]Bring about clarification -> xor: [(-300, 425)(-300, 475)]Rejection created -> Inform customer: [(0, 525)(0, 575)]Clarification positive -> Create offer: [(-400, 625)(-400, 675)]xor -> Inquiry possibly feasible: [(-25, 200)(-300, 200)(-300, 275)]xor -> Inquiry not feasible: [(0, 225)(0, 275)]xor -> Inquiry feasible: [(25, 200)(200, 200)(200, 275)]";

          expect(setNodesAndEdges("test", undefined, nodes, edges, satelliteObjects, relations, 100, 50, 0, 0, true)).toEqual(musterErgebnis2);
      });
	
	
	
      it("MoHol Solution 1 [0, 10, 0, 0]", function () {
		
		  var nodes = [{id: '8', label: 'Inquiry received', type: 'event', group: 'MoHoL Solution 01', color: '#FF8080', level: 1}
,{id: '9', label: 'Check feasability', type: 'function', group: 'MoHoL Solution 01', color: '#80ff80', level: 2}
,{id: '26', label: 'xor', type: 'operator', group: 'MoHoL Solution 01', color: 'gray', level: 3}
,{id: '11', label: 'Inquiry possibly feasible', type: 'event', group: 'MoHoL Solution 01', color: '#FF8080', level: 4}
,{id: '23', label: 'Bring about clarification', type: 'function', group: 'MoHoL Solution 01', color: '#80ff80', level: 5}
,{id: '22', label: 'xor', type: 'operator', group: 'MoHoL Solution 01', color: 'gray', level: 6}
,{id: '25', label: 'Clarification positive', type: 'event', group: 'MoHoL Solution 01', color: '#FF8080', level: 7}
,{id: '15', label: 'Create offer', type: 'function', group: 'MoHoL Solution 01', color: '#80ff80', level: 8}
,{id: '3', label: 'Offer created', type: 'event', group: 'MoHoL Solution 01', color: '#FF8080', level: 9}
,{id: '18', label: 'Inform Customer', type: 'function', group: 'MoHoL Solution 01', color: '#80ff80', level: 10}
,{id: '21', label: 'Customer informed', type: 'event', group: 'MoHoL Solution 01', color: '#FF8080', level: 11}
,{id: '17', label: 'Clarification negative', type: 'event', group: 'MoHoL Solution 01', color: '#FF8080', level: 7}
,{id: '1', label: 'Create rejection', type: 'function', group: 'MoHoL Solution 01', color: '#80ff80', level: 8}
,{id: '4', label: 'Rejection created', type: 'event', group: 'MoHoL Solution 01', color: '#FF8080', level: 9}
,{id: '13', label: 'Inform Customer', type: 'function', group: 'MoHoL Solution 01', color: '#80ff80', level: 10}
,{id: '7', label: 'Customer informed', type: 'event', group: 'MoHoL Solution 01', color: '#FF8080', level: 11}
,{id: '5', label: 'Inquiry not feasible', type: 'event', group: 'MoHoL Solution 01', color: '#FF8080', level: 4}
,{id: '16', label: 'Create rejection', type: 'function', group: 'MoHoL Solution 01', color: '#80ff80', level: 5}
,{id: '24', label: 'Rejection created', type: 'event', group: 'MoHoL Solution 01', color: '#FF8080', level: 6}
,{id: '20', label: 'Inform customer', type: 'function', group: 'MoHoL Solution 01', color: '#80ff80', level: 7}
,{id: '2', label: 'Customer informed', type: 'event', group: 'MoHoL Solution 01', color: '#FF8080', level: 8}
,{id: '6', label: 'Inquiry feasible', type: 'event', group: 'MoHoL Solution 01', color: '#FF8080', level: 4}
,{id: '10', label: 'Create offer', type: 'function', group: 'MoHoL Solution 01', color: '#80ff80', level: 5}
,{id: '14', label: 'Offer created', type: 'event', group: 'MoHoL Solution 01', color: '#FF8080', level: 6}
,{id: '12', label: 'Inform Customer', type: 'function', group: 'MoHoL Solution 01', color: '#80ff80', level: 7}
,{id: '19', label: 'Customer informed', type: 'event', group: 'MoHoL Solution 01', color: '#FF8080', level: 8}
]; edges = [{from: '1', to: '4', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '3', to: '18', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '4', to: '13', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '5', to: '16', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '6', to: '10', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '8', to: '9', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '9', to: '26', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '10', to: '14', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '11', to: '23', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '12', to: '19', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '13', to: '7', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '14', to: '12', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '15', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '16', to: '24', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '17', to: '1', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '18', to: '21', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '20', to: '2', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '22', to: '25', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '22', to: '17', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '23', to: '22', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '24', to: '20', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '25', to: '15', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '26', to: '11', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '26', to: '5', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '26', to: '6', arrows:'to', style: 'arrow', color: 'gray'}
]; satelliteObjects = []; relations = [];
		
          var musterErgebnis2 = "node Inquiry received: (0, 0)node Check feasability: (0, 60)node xor: (0, 120)node Inquiry possibly feasible: (-150, 180)node Bring about clarification: (-150, 240)node xor: (-150, 300)node Inquiry not feasible: (0, 180)node Create rejection: (0, 240)node Rejection created: (0, 300)node Inform customer: (0, 360)node Customer informed: (0, 420)node Inquiry feasible: (100, 180)node Create offer: (100, 240)node Offer created: (100, 300)node Inform Customer: (100, 360)node Customer informed: (100, 420)node Clarification positive: (-200, 360)node Create offer: (-200, 420)node Offer created: (-200, 480)node Inform Customer: (-200, 540)node Customer informed: (-200, 600)node Clarification negative: (-100, 360)node Create rejection: (-100, 420)node Rejection created: (-100, 480)node Inform Customer: (-100, 540)node Customer informed: (-100, 600)Create rejection -> Rejection created: [(-100, 445)(-100, 455)]Offer created -> Inform Customer: [(-200, 505)(-200, 515)]Rejection created -> Inform Customer: [(-100, 505)(-100, 515)]Inquiry not feasible -> Create rejection: [(0, 205)(0, 215)]Inquiry feasible -> Create offer: [(100, 205)(100, 215)]Inquiry received -> Check feasability: [(0, 25)(0, 35)]Check feasability -> xor: [(0, 85)(0, 95)]Create offer -> Offer created: [(100, 265)(100, 275)]Inquiry possibly feasible -> Bring about clarification: [(-150, 205)(-150, 215)]Inform Customer -> Customer informed: [(100, 385)(100, 395)]Inform Customer -> Customer informed: [(-100, 565)(-100, 575)]Offer created -> Inform Customer: [(100, 325)(100, 335)]Create offer -> Offer created: [(-200, 445)(-200, 455)]Create rejection -> Rejection created: [(0, 265)(0, 275)]Clarification negative -> Create rejection: [(-100, 385)(-100, 395)]Inform Customer -> Customer informed: [(-200, 565)(-200, 575)]Inform customer -> Customer informed: [(0, 385)(0, 395)]xor -> Clarification positive: [(-175, 300)(-200, 300)(-200, 335)]xor -> Clarification negative: [(-125, 300)(-100, 300)(-100, 335)]Bring about clarification -> xor: [(-150, 265)(-150, 275)]Rejection created -> Inform customer: [(0, 325)(0, 335)]Clarification positive -> Create offer: [(-200, 385)(-200, 395)]xor -> Inquiry possibly feasible: [(-25, 120)(-150, 120)(-150, 155)]xor -> Inquiry not feasible: [(0, 145)(0, 155)]xor -> Inquiry feasible: [(25, 120)(100, 120)(100, 155)]";

          expect(setNodesAndEdges("test", undefined, nodes, edges, satelliteObjects, relations, 0, 10, 0, 0, true)).toEqual(musterErgebnis2);
      });
	  
	        it("MoHol Solution 2 [100, 50, 0, 0]", function () {
		
		  var nodes = [{id: '16', label: 'Inquiry received', type: 'event', group: 'MoHoL Solution 02', color: '#FF8080', level: 1}
,{id: '9', label: 'Check feasability', type: 'function', group: 'MoHoL Solution 02', color: '#80ff80', level: 2}
,{id: '1', label: 'xor', type: 'operator', group: 'MoHoL Solution 02', color: 'gray', level: 3}
,{id: '13', label: 'Inquiry not feasible', type: 'event', group: 'MoHoL Solution 02', color: '#FF8080', level: 4}
,{id: '2', label: 'xor', type: 'operator', group: 'MoHoL Solution 02', color: 'gray', level: 5}
,{id: '20', label: 'Create rejection', type: 'function', group: 'MoHoL Solution 02', color: '#80ff80', level: 6}
,{id: '8', label: 'Rejection created', type: 'event', group: 'MoHoL Solution 02', color: '#FF8080', level: 7}
,{id: '5', label: 'Inform customer', type: 'function', group: 'MoHoL Solution 02', color: '#80ff80', level: 8}
,{id: '7', label: 'Customer informed', type: 'event', group: 'MoHoL Solution 02', color: '#FF8080', level: 9}
,{id: '14', label: 'Inquiry possibly feasible', type: 'event', group: 'MoHoL Solution 02', color: '#FF8080', level: 4}
,{id: '11', label: 'Bring about clarification', type: 'function', group: 'MoHoL Solution 02', color: '#80ff80', level: 5}
,{id: '4', label: 'xor', type: 'operator', group: 'MoHoL Solution 02', color: 'gray', level: 6}
,{id: '10', label: 'Clarification negative', type: 'event', group: 'MoHoL Solution 02', color: '#FF8080', level: 7}
,{id: '3', label: 'Clarification positive', type: 'event', group: 'MoHoL Solution 02', color: '#FF8080', level: 7}
,{id: '15', label: 'xor', type: 'operator', group: 'MoHoL Solution 02', color: 'gray', level: 8}
,{id: '6', label: 'Create offer', type: 'function', group: 'MoHoL Solution 02', color: '#80ff80', level: 9}
,{id: '12', label: 'Offer created', type: 'event', group: 'MoHoL Solution 02', color: '#FF8080', level: 10}
,{id: '18', label: 'Inform customer', type: 'function', group: 'MoHoL Solution 02', color: '#80ff80', level: 11}
,{id: '19', label: 'Customer informed', type: 'event', group: 'MoHoL Solution 02', color: '#FF8080', level: 12}
,{id: '17', label: 'Inquiry feasible', type: 'event', group: 'MoHoL Solution 02', color: '#FF8080', level: 4}
]; edges = [{from: '1', to: '13', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '1', to: '14', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '1', to: '17', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '2', to: '20', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '3', to: '15', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '4', to: '10', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '4', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '5', to: '7', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '6', to: '12', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '8', to: '5', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '9', to: '1', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '10', to: '2', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '11', to: '4', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '12', to: '18', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '13', to: '2', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '14', to: '11', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '15', to: '6', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '16', to: '9', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '17', to: '15', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '18', to: '19', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '20', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
]; satelliteObjects = []; relations = [];
		
          var musterErgebnis = "node Inquiry received: (0, 0)node Check feasability: (0, 100)node xor: (0, 200)node Inquiry not feasible: (-300, 300)node Inquiry possibly feasible: (0, 300)node Bring about clarification: (0, 400)node xor: (0, 500)node Inquiry feasible: (300, 300)node Clarification negative: (-100, 600)node Clarification positive: (100, 600)node xor: (-200, 700)node Create rejection: (-200, 800)node Rejection created: (-200, 900)node Inform customer: (-200, 1000)node Customer informed: (-200, 1100)node xor: (200, 700)node Create offer: (200, 800)node Offer created: (200, 900)node Inform customer: (200, 1000)node Customer informed: (200, 1100)xor -> Inquiry not feasible: [(-25, 200)(-300, 200)(-300, 275)]xor -> Inquiry possibly feasible: [(0, 225)(0, 275)]xor -> Inquiry feasible: [(25, 200)(300, 200)(300, 275)]xor -> Create rejection: [(-200, 725)(-200, 775)]Clarification positive -> xor: [(100, 625)(100, 700)(175, 700)]xor -> Clarification negative: [(-25, 500)(-100, 500)(-100, 575)]xor -> Clarification positive: [(25, 500)(100, 500)(100, 575)]Inform customer -> Customer informed: [(-200, 1025)(-200, 1075)]Create offer -> Offer created: [(200, 825)(200, 875)]Rejection created -> Inform customer: [(-200, 925)(-200, 975)]Check feasability -> xor: [(0, 125)(0, 175)]Clarification negative -> xor: [(-100, 625)(-100, 700)(-175, 700)]Bring about clarification -> xor: [(0, 425)(0, 475)]Offer created -> Inform customer: [(200, 925)(200, 975)]Inquiry not feasible -> xor: [(-300, 325)(-300, 700)(-225, 700)]Inquiry possibly feasible -> Bring about clarification: [(0, 325)(0, 375)]xor -> Create offer: [(200, 725)(200, 775)]Inquiry received -> Check feasability: [(0, 25)(0, 75)]Inquiry feasible -> xor: [(300, 325)(300, 700)(225, 700)]Inform customer -> Customer informed: [(200, 1025)(200, 1075)]Create rejection -> Rejection created: [(-200, 825)(-200, 875)]";

          expect(setNodesAndEdges("test", undefined, nodes, edges, satelliteObjects, relations, 100, 50, 0, 0, true)).toEqual(musterErgebnis);
      });
	
	
	
      it("MoHol Solution 2 [0, 10, 0, 0]", function () {
		
		  var  nodes = [{id: '16', label: 'Inquiry received', type: 'event', group: 'MoHoL Solution 02', color: '#FF8080', level: 1}
,{id: '9', label: 'Check feasability', type: 'function', group: 'MoHoL Solution 02', color: '#80ff80', level: 2}
,{id: '1', label: 'xor', type: 'operator', group: 'MoHoL Solution 02', color: 'gray', level: 3}
,{id: '13', label: 'Inquiry not feasible', type: 'event', group: 'MoHoL Solution 02', color: '#FF8080', level: 4}
,{id: '2', label: 'xor', type: 'operator', group: 'MoHoL Solution 02', color: 'gray', level: 5}
,{id: '20', label: 'Create rejection', type: 'function', group: 'MoHoL Solution 02', color: '#80ff80', level: 6}
,{id: '8', label: 'Rejection created', type: 'event', group: 'MoHoL Solution 02', color: '#FF8080', level: 7}
,{id: '5', label: 'Inform customer', type: 'function', group: 'MoHoL Solution 02', color: '#80ff80', level: 8}
,{id: '7', label: 'Customer informed', type: 'event', group: 'MoHoL Solution 02', color: '#FF8080', level: 9}
,{id: '14', label: 'Inquiry possibly feasible', type: 'event', group: 'MoHoL Solution 02', color: '#FF8080', level: 4}
,{id: '11', label: 'Bring about clarification', type: 'function', group: 'MoHoL Solution 02', color: '#80ff80', level: 5}
,{id: '4', label: 'xor', type: 'operator', group: 'MoHoL Solution 02', color: 'gray', level: 6}
,{id: '10', label: 'Clarification negative', type: 'event', group: 'MoHoL Solution 02', color: '#FF8080', level: 7}
,{id: '3', label: 'Clarification positive', type: 'event', group: 'MoHoL Solution 02', color: '#FF8080', level: 7}
,{id: '15', label: 'xor', type: 'operator', group: 'MoHoL Solution 02', color: 'gray', level: 8}
,{id: '6', label: 'Create offer', type: 'function', group: 'MoHoL Solution 02', color: '#80ff80', level: 9}
,{id: '12', label: 'Offer created', type: 'event', group: 'MoHoL Solution 02', color: '#FF8080', level: 10}
,{id: '18', label: 'Inform customer', type: 'function', group: 'MoHoL Solution 02', color: '#80ff80', level: 11}
,{id: '19', label: 'Customer informed', type: 'event', group: 'MoHoL Solution 02', color: '#FF8080', level: 12}
,{id: '17', label: 'Inquiry feasible', type: 'event', group: 'MoHoL Solution 02', color: '#FF8080', level: 4}
]; edges = [{from: '1', to: '13', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '1', to: '14', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '1', to: '17', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '2', to: '20', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '3', to: '15', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '4', to: '10', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '4', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '5', to: '7', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '6', to: '12', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '8', to: '5', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '9', to: '1', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '10', to: '2', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '11', to: '4', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '12', to: '18', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '13', to: '2', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '14', to: '11', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '15', to: '6', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '16', to: '9', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '17', to: '15', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '18', to: '19', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '20', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
]; satelliteObjects = []; relations = [];
		
          var musterErgebnis = "node Inquiry received: (0, 0)node Check feasability: (0, 60)node xor: (0, 120)node Inquiry not feasible: (-150, 180)node Inquiry possibly feasible: (0, 180)node Bring about clarification: (0, 240)node xor: (0, 300)node Inquiry feasible: (150, 180)node Clarification negative: (-50, 360)node Clarification positive: (50, 360)node xor: (-100, 420)node Create rejection: (-100, 480)node Rejection created: (-100, 540)node Inform customer: (-100, 600)node Customer informed: (-100, 660)node xor: (100, 420)node Create offer: (100, 480)node Offer created: (100, 540)node Inform customer: (100, 600)node Customer informed: (100, 660)xor -> Inquiry not feasible: [(-25, 120)(-150, 120)(-150, 155)]xor -> Inquiry possibly feasible: [(0, 145)(0, 155)]xor -> Inquiry feasible: [(25, 120)(150, 120)(150, 155)]xor -> Create rejection: [(-100, 445)(-100, 455)]Clarification positive -> xor: [(50, 385)(50, 420)(75, 420)]xor -> Clarification negative: [(-25, 300)(-50, 300)(-50, 335)]xor -> Clarification positive: [(25, 300)(50, 300)(50, 335)]Inform customer -> Customer informed: [(-100, 625)(-100, 635)]Create offer -> Offer created: [(100, 505)(100, 515)]Rejection created -> Inform customer: [(-100, 565)(-100, 575)]Check feasability -> xor: [(0, 85)(0, 95)]Clarification negative -> xor: [(-50, 385)(-50, 420)(-75, 420)]Bring about clarification -> xor: [(0, 265)(0, 275)]Offer created -> Inform customer: [(100, 565)(100, 575)]Inquiry not feasible -> xor: [(-150, 205)(-150, 420)(-125, 420)]Inquiry possibly feasible -> Bring about clarification: [(0, 205)(0, 215)]xor -> Create offer: [(100, 445)(100, 455)]Inquiry received -> Check feasability: [(0, 25)(0, 35)]Inquiry feasible -> xor: [(150, 205)(150, 420)(125, 420)]Inform customer -> Customer informed: [(100, 625)(100, 635)]Create rejection -> Rejection created: [(-100, 505)(-100, 515)]";

          expect(setNodesAndEdges("test", undefined, nodes, edges, satelliteObjects, relations, 0, 10, 0, 0, true)).toEqual(musterErgebnis);
      });



      it("MoHol Solution 7 [100, 50, 0, 0]", function () {
		
		  var nodes = [{id: '1', label: 'Inquiry received', type: 'event', group: 'MoHoL Solution 07', color: '#FF8080', level: 1}
,{id: '17', label: 'Check feasibility', type: 'function', group: 'MoHoL Solution 07', color: '#80ff80', level: 2}
,{id: '3', label: 'or', type: 'operator', group: 'MoHoL Solution 07', color: 'gray', level: 3}
,{id: '15', label: 'Inquiry not feasible', type: 'event', group: 'MoHoL Solution 07', color: '#FF8080', level: 4}
,{id: '4', label: 'or', type: 'operator', group: 'MoHoL Solution 07', color: 'gray', level: 5}
,{id: '18', label: 'Create rejection', type: 'function', group: 'MoHoL Solution 07', color: '#80ff80', level: 6}
,{id: '19', label: 'Rejection created', type: 'event', group: 'MoHoL Solution 07', color: '#FF8080', level: 7}
,{id: '9', label: 'xor', type: 'operator', group: 'MoHoL Solution 07', color: 'gray', level: 8}
,{id: '12', label: 'Inform customer', type: 'function', group: 'MoHoL Solution 07', color: '#80ff80', level: 9}
,{id: '6', label: 'Customer informed', type: 'event', group: 'MoHoL Solution 07', color: '#FF8080', level: 10}
,{id: '16', label: 'Inquiry possibly feasible', type: 'event', group: 'MoHoL Solution 07', color: '#FF8080', level: 4}
,{id: '5', label: 'Bring about clarification', type: 'function', group: 'MoHoL Solution 07', color: '#80ff80', level: 5}
,{id: '8', label: 'or', type: 'operator', group: 'MoHoL Solution 07', color: 'gray', level: 6}
,{id: '10', label: 'Clarification negative', type: 'event', group: 'MoHoL Solution 07', color: '#FF8080', level: 7}
,{id: '14', label: 'Clarification positive', type: 'event', group: 'MoHoL Solution 07', color: '#FF8080', level: 7}
,{id: '11', label: 'or', type: 'operator', group: 'MoHoL Solution 07', color: 'gray', level: 8}
,{id: '2', label: 'Create offer', type: 'function', group: 'MoHoL Solution 07', color: '#80ff80', level: 9}
,{id: '13', label: 'Offer created', type: 'event', group: 'MoHoL Solution 07', color: '#FF8080', level: 10}
,{id: '7', label: 'Inquiry feasible', type: 'event', group: 'MoHoL Solution 07', color: '#FF8080', level: 4}
]; edges = [{from: '1', to: '17', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '2', to: '13', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '3', to: '15', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '3', to: '16', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '3', to: '7', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '4', to: '18', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '5', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '7', to: '11', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '8', to: '10', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '8', to: '14', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '9', to: '12', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '10', to: '4', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '11', to: '2', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '12', to: '6', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '13', to: '9', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '14', to: '11', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '15', to: '4', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '16', to: '5', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '17', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '18', to: '19', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '19', to: '9', arrows:'to', style: 'arrow', color: 'gray'}
]; satelliteObjects = []; relations = [];
		
          var musterErgebnis = "node Inquiry received: (0, 0)node Check feasibility: (0, 100)node or: (0, 200)node Inquiry not feasible: (-300, 300)node Inquiry possibly feasible: (0, 300)node Bring about clarification: (0, 400)node or: (0, 500)node Inquiry feasible: (300, 300)node Clarification negative: (-100, 600)node Clarification positive: (100, 600)node or: (-200, 700)node Create rejection: (-200, 800)node Rejection created: (-200, 900)node or: (200, 700)node Create offer: (200, 800)node Offer created: (200, 900)node xor: (0, 1000)node Inform customer: (0, 1100)node Customer informed: (0, 1200)Inquiry received -> Check feasibility: [(0, 25)(0, 75)]Create offer -> Offer created: [(200, 825)(200, 875)]or -> Inquiry not feasible: [(-25, 200)(-300, 200)(-300, 275)]or -> Inquiry possibly feasible: [(0, 225)(0, 275)]or -> Inquiry feasible: [(25, 200)(300, 200)(300, 275)]or -> Create rejection: [(-200, 725)(-200, 775)]Bring about clarification -> or: [(0, 425)(0, 475)]Inquiry feasible -> or: [(300, 325)(300, 700)(225, 700)]or -> Clarification negative: [(-25, 500)(-100, 500)(-100, 575)]or -> Clarification positive: [(25, 500)(100, 500)(100, 575)]xor -> Inform customer: [(0, 1025)(0, 1075)]Clarification negative -> or: [(-100, 625)(-100, 700)(-175, 700)]or -> Create offer: [(200, 725)(200, 775)]Inform customer -> Customer informed: [(0, 1125)(0, 1175)]Offer created -> xor: [(200, 925)(200, 1000)(25, 1000)]Clarification positive -> or: [(100, 625)(100, 700)(175, 700)]Inquiry not feasible -> or: [(-300, 325)(-300, 700)(-225, 700)]Inquiry possibly feasible -> Bring about clarification: [(0, 325)(0, 375)]Check feasibility -> or: [(0, 125)(0, 175)]Create rejection -> Rejection created: [(-200, 825)(-200, 875)]Rejection created -> xor: [(-200, 925)(-200, 1000)(-25, 1000)]";

          expect(setNodesAndEdges("test", undefined, nodes, edges, satelliteObjects, relations, 100, 50, 0, 0, true)).toEqual(musterErgebnis);
      });
	
	
	
      it("MoHol Solution 7 [0, 10, 0, 0]", function () {
		
		  var  nodes = [{id: '1', label: 'Inquiry received', type: 'event', group: 'MoHoL Solution 07', color: '#FF8080', level: 1}
,{id: '17', label: 'Check feasibility', type: 'function', group: 'MoHoL Solution 07', color: '#80ff80', level: 2}
,{id: '3', label: 'or', type: 'operator', group: 'MoHoL Solution 07', color: 'gray', level: 3}
,{id: '15', label: 'Inquiry not feasible', type: 'event', group: 'MoHoL Solution 07', color: '#FF8080', level: 4}
,{id: '4', label: 'or', type: 'operator', group: 'MoHoL Solution 07', color: 'gray', level: 5}
,{id: '18', label: 'Create rejection', type: 'function', group: 'MoHoL Solution 07', color: '#80ff80', level: 6}
,{id: '19', label: 'Rejection created', type: 'event', group: 'MoHoL Solution 07', color: '#FF8080', level: 7}
,{id: '9', label: 'xor', type: 'operator', group: 'MoHoL Solution 07', color: 'gray', level: 8}
,{id: '12', label: 'Inform customer', type: 'function', group: 'MoHoL Solution 07', color: '#80ff80', level: 9}
,{id: '6', label: 'Customer informed', type: 'event', group: 'MoHoL Solution 07', color: '#FF8080', level: 10}
,{id: '16', label: 'Inquiry possibly feasible', type: 'event', group: 'MoHoL Solution 07', color: '#FF8080', level: 4}
,{id: '5', label: 'Bring about clarification', type: 'function', group: 'MoHoL Solution 07', color: '#80ff80', level: 5}
,{id: '8', label: 'or', type: 'operator', group: 'MoHoL Solution 07', color: 'gray', level: 6}
,{id: '10', label: 'Clarification negative', type: 'event', group: 'MoHoL Solution 07', color: '#FF8080', level: 7}
,{id: '14', label: 'Clarification positive', type: 'event', group: 'MoHoL Solution 07', color: '#FF8080', level: 7}
,{id: '11', label: 'or', type: 'operator', group: 'MoHoL Solution 07', color: 'gray', level: 8}
,{id: '2', label: 'Create offer', type: 'function', group: 'MoHoL Solution 07', color: '#80ff80', level: 9}
,{id: '13', label: 'Offer created', type: 'event', group: 'MoHoL Solution 07', color: '#FF8080', level: 10}
,{id: '7', label: 'Inquiry feasible', type: 'event', group: 'MoHoL Solution 07', color: '#FF8080', level: 4}
]; edges = [{from: '1', to: '17', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '2', to: '13', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '3', to: '15', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '3', to: '16', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '3', to: '7', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '4', to: '18', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '5', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '7', to: '11', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '8', to: '10', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '8', to: '14', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '9', to: '12', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '10', to: '4', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '11', to: '2', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '12', to: '6', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '13', to: '9', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '14', to: '11', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '15', to: '4', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '16', to: '5', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '17', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '18', to: '19', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '19', to: '9', arrows:'to', style: 'arrow', color: 'gray'}
]; satelliteObjects = []; relations = [];
		
          var musterErgebnis = "node Inquiry received: (0, 0)node Check feasibility: (0, 60)node or: (0, 120)node Inquiry not feasible: (-150, 180)node Inquiry possibly feasible: (0, 180)node Bring about clarification: (0, 240)node or: (0, 300)node Inquiry feasible: (150, 180)node Clarification negative: (-50, 360)node Clarification positive: (50, 360)node or: (-100, 420)node Create rejection: (-100, 480)node Rejection created: (-100, 540)node or: (100, 420)node Create offer: (100, 480)node Offer created: (100, 540)node xor: (0, 600)node Inform customer: (0, 660)node Customer informed: (0, 720)Inquiry received -> Check feasibility: [(0, 25)(0, 35)]Create offer -> Offer created: [(100, 505)(100, 515)]or -> Inquiry not feasible: [(-25, 120)(-150, 120)(-150, 155)]or -> Inquiry possibly feasible: [(0, 145)(0, 155)]or -> Inquiry feasible: [(25, 120)(150, 120)(150, 155)]or -> Create rejection: [(-100, 445)(-100, 455)]Bring about clarification -> or: [(0, 265)(0, 275)]Inquiry feasible -> or: [(150, 205)(150, 420)(125, 420)]or -> Clarification negative: [(-25, 300)(-50, 300)(-50, 335)]or -> Clarification positive: [(25, 300)(50, 300)(50, 335)]xor -> Inform customer: [(0, 625)(0, 635)]Clarification negative -> or: [(-50, 385)(-50, 420)(-75, 420)]or -> Create offer: [(100, 445)(100, 455)]Inform customer -> Customer informed: [(0, 685)(0, 695)]Offer created -> xor: [(100, 565)(100, 600)(25, 600)]Clarification positive -> or: [(50, 385)(50, 420)(75, 420)]Inquiry not feasible -> or: [(-150, 205)(-150, 420)(-125, 420)]Inquiry possibly feasible -> Bring about clarification: [(0, 205)(0, 215)]Check feasibility -> or: [(0, 85)(0, 95)]Create rejection -> Rejection created: [(-100, 505)(-100, 515)]Rejection created -> xor: [(-100, 565)(-100, 600)(-25, 600)]";

          expect(setNodesAndEdges("test", undefined, nodes, edges, satelliteObjects, relations, 0, 10, 0, 0, true)).toEqual(musterErgebnis);
      });
	  
	       it("Exams Sample Solution [100, 50, 0, 0]", function () {
		
		  var nodes = [{id: '17', label: 'BTA required', type: 'event', group: 'Sample_Solution', color: '#FF8080', level: 1}
,{id: '9', label: 'fill BTA', type: 'function', group: 'Sample_Solution', color: '#80ff80', level: 2}
,{id: '24', label: 'or', type: 'operator', group: 'Sample_Solution', color: 'gray', level: 3}
,{id: '2', label: 'BTA completed', type: 'event', group: 'Sample_Solution', color: '#FF8080', level: 4}
,{id: '41', label: 'handing in BTA', type: 'function', group: 'Sample_Solution', color: '#80ff80', level: 5}
,{id: '12', label: 'BTA handed in', type: 'event', group: 'Sample_Solution', color: '#FF8080', level: 6}
,{id: '36', label: 'reviewing BTA', type: 'function', group: 'Sample_Solution', color: '#80ff80', level: 7}
,{id: '26', label: 'xor', type: 'operator', group: 'Sample_Solution', color: 'gray', level: 8}
,{id: '7', label: 'BTA not in accordance with requirements', type: 'event', group: 'Sample_Solution', color: '#FF8080', level: 9}
,{id: '25', label: 'xor', type: 'operator', group: 'Sample_Solution', color: 'gray', level: 10}
,{id: '1', label: 'noting match with requirements', type: 'function', group: 'Sample_Solution', color: '#80ff80', level: 11}
,{id: '14', label: 'match noted', type: 'event', group: 'Sample_Solution', color: '#FF8080', level: 12}
,{id: '18', label: 'handing in at the manager\'s office', type: 'function', group: 'Sample_Solution', color: '#80ff80', level: 13}
,{id: '29', label: 'xor', type: 'operator', group: 'Sample_Solution', color: 'gray', level: 14}
,{id: '39', label: 'BTA approved', type: 'event', group: 'Sample_Solution', color: '#FF8080', level: 15}
,{id: '3', label: 'noting of employee and trip period', type: 'function', group: 'Sample_Solution', color: '#80ff80', level: 16}
,{id: '44', label: 'BT registered', type: 'event', group: 'Sample_Solution', color: '#FF8080', level: 17}
,{id: '8', label: 'xor', type: 'operator', group: 'Sample_Solution', color: 'gray', level: 18}
,{id: '6', label: 'informing employee', type: 'function', group: 'Sample_Solution', color: '#80ff80', level: 19}
,{id: '10', label: 'xor', type: 'operator', group: 'Sample_Solution', color: 'gray', level: 20}
,{id: '32', label: 'BT rejected', type: 'event', group: 'Sample_Solution', color: '#FF8080', level: 21}
,{id: '20', label: 'reviewing decision', type: 'function', group: 'Sample_Solution', color: '#80ff80', level: 22}
,{id: '33', label: 'xor', type: 'operator', group: 'Sample_Solution', color: 'gray', level: 23}
,{id: '15', label: 'BT not rejected', type: 'event', group: 'Sample_Solution', color: '#FF8080', level: 24}
,{id: '34', label: 'revising BTA', type: 'function', group: 'Sample_Solution', color: '#80ff80', level: 25}
,{id: '27', label: 'BT rejected', type: 'event', group: 'Sample_Solution', color: '#FF8080', level: 24}
,{id: '13', label: 'BT approved', type: 'event', group: 'Sample_Solution', color: '#FF8080', level: 21}
,{id: '16', label: 'checking availability of company car', type: 'function', group: 'Sample_Solution', color: '#80ff80', level: 22}
,{id: '23', label: 'xor', type: 'operator', group: 'Sample_Solution', color: 'gray', level: 23}
,{id: '31', label: 'company car available', type: 'event', group: 'Sample_Solution', color: '#FF8080', level: 24}
,{id: '35', label: 'booking company car', type: 'function', group: 'Sample_Solution', color: '#80ff80', level: 25}
,{id: '4', label: 'company car booked', type: 'event', group: 'Sample_Solution', color: '#FF8080', level: 26}
,{id: '40', label: 'xor', type: 'operator', group: 'Sample_Solution', color: 'gray', level: 27}
,{id: '19', label: 'and', type: 'operator', group: 'Sample_Solution', color: 'gray', level: 28}
,{id: '42', label: 'realizing BT', type: 'function', group: 'Sample_Solution', color: '#80ff80', level: 29}
,{id: '38', label: 'BT realized', type: 'event', group: 'Sample_Solution', color: '#FF8080', level: 30}
,{id: '11', label: 'accounting for BT', type: 'function', group: 'Sample_Solution', color: '#80ff80', level: 31}
,{id: '37', label: 'BT accounted', type: 'event', group: 'Sample_Solution', color: '#FF8080', level: 32}
,{id: '22', label: 'company car not available', type: 'event', group: 'Sample_Solution', color: '#FF8080', level: 24}
,{id: '21', label: 'booking rental car', type: 'function', group: 'Sample_Solution', color: '#80ff80', level: 25}
,{id: '30', label: 'rental car booked', type: 'event', group: 'Sample_Solution', color: '#FF8080', level: 26}
,{id: '43', label: 'BTA rejected', type: 'event', group: 'Sample_Solution', color: '#FF8080', level: 15}
,{id: '5', label: 'BTA in accordance with requirements', type: 'event', group: 'Sample_Solution', color: '#FF8080', level: 9}
,{id: '28', label: 'date of BT reached', type: 'event', group: 'Sample_Solution', color: '#FF8080', level: 1}
]; edges = [{from: '1', to: '14', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '2', to: '41', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '3', to: '44', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '4', to: '40', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '5', to: '25', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '6', to: '10', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '7', to: '25', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '8', to: '6', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '9', to: '24', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '10', to: '32', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '10', to: '13', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '11', to: '37', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '12', to: '36', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '13', to: '16', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '14', to: '18', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '15', to: '34', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '16', to: '23', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '17', to: '9', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '18', to: '29', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '19', to: '42', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '20', to: '33', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '21', to: '30', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '22', to: '21', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '23', to: '31', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '23', to: '22', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '24', to: '2', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '25', to: '1', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '26', to: '7', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '26', to: '5', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '28', to: '19', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '29', to: '39', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '29', to: '43', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '30', to: '40', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '31', to: '35', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '32', to: '20', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '33', to: '15', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '33', to: '27', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '34', to: '24', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '35', to: '4', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '36', to: '26', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '38', to: '11', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '39', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '40', to: '19', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '41', to: '12', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '42', to: '38', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '43', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '44', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
]; satelliteObjects = []; relations = [];
		
          var musterErgebnis = "node and: (0, 0)node realizing BT: (0, 100)node BT realized: (0, 200)node accounting for BT: (0, 300)node BT accounted: (0, 400)node xor: (87.5, -1000)node informing employee: (87.5, -900)node xor: (87.5, -800)node BT rejected: (-112.5, -700)node reviewing decision: (-112.5, -600)node xor: (-112.5, -500)node BT approved: (287.5, -700)node checking availability of company car: (287.5, -600)node xor: (287.5, -500)node BT not rejected: (-212.5, -400)node revising BTA: (-212.5, -300)node BT rejected: (-12.5, -400)node company car available: (187.5, -400)node booking company car: (187.5, -300)node company car booked: (187.5, -200)node company car not available: (387.5, -400)node booking rental car: (387.5, -300)node rental car booked: (387.5, -200)node xor: (287.5, -100)node date of BT reached: (-100, -100)node or: (87.5, -2500)node BTA completed: (87.5, -2400)node handing in BTA: (87.5, -2300)node BTA handed in: (87.5, -2200)node reviewing BTA: (87.5, -2100)node xor: (87.5, -2000)node BTA not in accordance with requirements: (-12.5, -1900)node BTA in accordance with requirements: (187.5, -1900)node xor: (87.5, -1800)node noting match with requirements: (87.5, -1700)node match noted: (87.5, -1600)node handing in at the manager's office: (87.5, -1500)node xor: (87.5, -1400)node BTA approved: (-12.5, -1300)node noting of employee and trip period: (-12.5, -1200)node BT registered: (-12.5, -1100)node BTA rejected: (187.5, -1300)node BTA required: (87.5, -2700)node fill BTA: (87.5, -2600)noting match with requirements -> match noted: [(87.5, -1675)(87.5, -1625)]BTA completed -> handing in BTA: [(87.5, -2375)(87.5, -2325)]noting of employee and trip period -> BT registered: [(-12.5, -1175)(-12.5, -1125)]company car booked -> xor: [(187.5, -175)(187.5, -100)(262.5, -100)]BTA in accordance with requirements -> xor: [(187.5, -1875)(187.5, -1800)(112.5, -1800)]informing employee -> xor: [(87.5, -875)(87.5, -825)]BTA not in accordance with requirements -> xor: [(-12.5, -1875)(-12.5, -1800)(62.5, -1800)]xor -> informing employee: [(87.5, -975)(87.5, -925)]fill BTA -> or: [(87.5, -2575)(87.5, -2525)]xor -> BT rejected: [(62.5, -800)(-112.5, -800)(-112.5, -725)]xor -> BT approved: [(112.5, -800)(287.5, -800)(287.5, -725)]accounting for BT -> BT accounted: [(0, 325)(0, 375)]BTA handed in -> reviewing BTA: [(87.5, -2175)(87.5, -2125)]BT approved -> checking availability of company car: [(287.5, -675)(287.5, -625)]match noted -> handing in at the manager's office: [(87.5, -1575)(87.5, -1525)]BT not rejected -> revising BTA: [(-212.5, -375)(-212.5, -325)]checking availability of company car -> xor: [(287.5, -575)(287.5, -525)]BTA required -> fill BTA: [(87.5, -2675)(87.5, -2625)]handing in at the manager's office -> xor: [(87.5, -1475)(87.5, -1425)]and -> realizing BT: [(0, 25)(0, 75)]reviewing decision -> xor: [(-112.5, -575)(-112.5, -525)]booking rental car -> rental car booked: [(387.5, -275)(387.5, -225)]company car not available -> booking rental car: [(387.5, -375)(387.5, -325)]xor -> company car available: [(262.5, -500)(187.5, -500)(187.5, -425)]xor -> company car not available: [(312.5, -500)(387.5, -500)(387.5, -425)]or -> BTA completed: [(87.5, -2475)(87.5, -2425)]xor -> noting match with requirements: [(87.5, -1775)(87.5, -1725)]xor -> BTA not in accordance with requirements: [(62.5, -2000)(-12.5, -2000)(-12.5, -1925)]xor -> BTA in accordance with requirements: [(112.5, -2000)(187.5, -2000)(187.5, -1925)]date of BT reached -> and: [(-100, -75)(-100, 0)(-25, 0)]xor -> BTA approved: [(62.5, -1400)(-12.5, -1400)(-12.5, -1325)]xor -> BTA rejected: [(112.5, -1400)(187.5, -1400)(187.5, -1325)]rental car booked -> xor: [(387.5, -175)(387.5, -100)(312.5, -100)]company car available -> booking company car: [(187.5, -375)(187.5, -325)]BT rejected -> reviewing decision: [(-112.5, -675)(-112.5, -625)]xor -> BT not rejected: [(-137.5, -500)(-212.5, -500)(-212.5, -425)]xor -> BT rejected: [(-87.5, -500)(-12.5, -500)(-12.5, -425)]booking company car -> company car booked: [(187.5, -275)(187.5, -225)]reviewing BTA -> xor: [(87.5, -2075)(87.5, -2025)]BT realized -> accounting for BT: [(0, 225)(0, 275)]BTA approved -> noting of employee and trip period: [(-12.5, -1275)(-12.5, -1225)]xor -> and: [(287.5, -75)(287.5, 0)(25, 0)]handing in BTA -> BTA handed in: [(87.5, -2275)(87.5, -2225)]realizing BT -> BT realized: [(0, 125)(0, 175)]BTA rejected -> xor: [(187.5, -1275)(187.5, -1000)(112.5, -1000)]BT registered -> xor: [(-12.5, -1075)(-12.5, -1000)(62.5, -1000)]revising BTA -> or: [(-262.5, -300)(-362.5, -300)(-362.5, -2500)(62.5, -2500)]";

          expect(setNodesAndEdges("test", undefined, nodes, edges, satelliteObjects, relations, 100, 50, 0, 0, true)).toEqual(musterErgebnis);
      });
	
	
	
      it("Exams Sample Solution [0, 10, 0, 0]", function () {
		
		  var  nodes = [{id: '17', label: 'BTA required', type: 'event', group: 'Sample_Solution', color: '#FF8080', level: 1}
,{id: '9', label: 'fill BTA', type: 'function', group: 'Sample_Solution', color: '#80ff80', level: 2}
,{id: '24', label: 'or', type: 'operator', group: 'Sample_Solution', color: 'gray', level: 3}
,{id: '2', label: 'BTA completed', type: 'event', group: 'Sample_Solution', color: '#FF8080', level: 4}
,{id: '41', label: 'handing in BTA', type: 'function', group: 'Sample_Solution', color: '#80ff80', level: 5}
,{id: '12', label: 'BTA handed in', type: 'event', group: 'Sample_Solution', color: '#FF8080', level: 6}
,{id: '36', label: 'reviewing BTA', type: 'function', group: 'Sample_Solution', color: '#80ff80', level: 7}
,{id: '26', label: 'xor', type: 'operator', group: 'Sample_Solution', color: 'gray', level: 8}
,{id: '7', label: 'BTA not in accordance with requirements', type: 'event', group: 'Sample_Solution', color: '#FF8080', level: 9}
,{id: '25', label: 'xor', type: 'operator', group: 'Sample_Solution', color: 'gray', level: 10}
,{id: '1', label: 'noting match with requirements', type: 'function', group: 'Sample_Solution', color: '#80ff80', level: 11}
,{id: '14', label: 'match noted', type: 'event', group: 'Sample_Solution', color: '#FF8080', level: 12}
,{id: '18', label: 'handing in at the manager\'s office', type: 'function', group: 'Sample_Solution', color: '#80ff80', level: 13}
,{id: '29', label: 'xor', type: 'operator', group: 'Sample_Solution', color: 'gray', level: 14}
,{id: '39', label: 'BTA approved', type: 'event', group: 'Sample_Solution', color: '#FF8080', level: 15}
,{id: '3', label: 'noting of employee and trip period', type: 'function', group: 'Sample_Solution', color: '#80ff80', level: 16}
,{id: '44', label: 'BT registered', type: 'event', group: 'Sample_Solution', color: '#FF8080', level: 17}
,{id: '8', label: 'xor', type: 'operator', group: 'Sample_Solution', color: 'gray', level: 18}
,{id: '6', label: 'informing employee', type: 'function', group: 'Sample_Solution', color: '#80ff80', level: 19}
,{id: '10', label: 'xor', type: 'operator', group: 'Sample_Solution', color: 'gray', level: 20}
,{id: '32', label: 'BT rejected', type: 'event', group: 'Sample_Solution', color: '#FF8080', level: 21}
,{id: '20', label: 'reviewing decision', type: 'function', group: 'Sample_Solution', color: '#80ff80', level: 22}
,{id: '33', label: 'xor', type: 'operator', group: 'Sample_Solution', color: 'gray', level: 23}
,{id: '15', label: 'BT not rejected', type: 'event', group: 'Sample_Solution', color: '#FF8080', level: 24}
,{id: '34', label: 'revising BTA', type: 'function', group: 'Sample_Solution', color: '#80ff80', level: 25}
,{id: '27', label: 'BT rejected', type: 'event', group: 'Sample_Solution', color: '#FF8080', level: 24}
,{id: '13', label: 'BT approved', type: 'event', group: 'Sample_Solution', color: '#FF8080', level: 21}
,{id: '16', label: 'checking availability of company car', type: 'function', group: 'Sample_Solution', color: '#80ff80', level: 22}
,{id: '23', label: 'xor', type: 'operator', group: 'Sample_Solution', color: 'gray', level: 23}
,{id: '31', label: 'company car available', type: 'event', group: 'Sample_Solution', color: '#FF8080', level: 24}
,{id: '35', label: 'booking company car', type: 'function', group: 'Sample_Solution', color: '#80ff80', level: 25}
,{id: '4', label: 'company car booked', type: 'event', group: 'Sample_Solution', color: '#FF8080', level: 26}
,{id: '40', label: 'xor', type: 'operator', group: 'Sample_Solution', color: 'gray', level: 27}
,{id: '19', label: 'and', type: 'operator', group: 'Sample_Solution', color: 'gray', level: 28}
,{id: '42', label: 'realizing BT', type: 'function', group: 'Sample_Solution', color: '#80ff80', level: 29}
,{id: '38', label: 'BT realized', type: 'event', group: 'Sample_Solution', color: '#FF8080', level: 30}
,{id: '11', label: 'accounting for BT', type: 'function', group: 'Sample_Solution', color: '#80ff80', level: 31}
,{id: '37', label: 'BT accounted', type: 'event', group: 'Sample_Solution', color: '#FF8080', level: 32}
,{id: '22', label: 'company car not available', type: 'event', group: 'Sample_Solution', color: '#FF8080', level: 24}
,{id: '21', label: 'booking rental car', type: 'function', group: 'Sample_Solution', color: '#80ff80', level: 25}
,{id: '30', label: 'rental car booked', type: 'event', group: 'Sample_Solution', color: '#FF8080', level: 26}
,{id: '43', label: 'BTA rejected', type: 'event', group: 'Sample_Solution', color: '#FF8080', level: 15}
,{id: '5', label: 'BTA in accordance with requirements', type: 'event', group: 'Sample_Solution', color: '#FF8080', level: 9}
,{id: '28', label: 'date of BT reached', type: 'event', group: 'Sample_Solution', color: '#FF8080', level: 1}
]; edges = [{from: '1', to: '14', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '2', to: '41', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '3', to: '44', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '4', to: '40', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '5', to: '25', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '6', to: '10', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '7', to: '25', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '8', to: '6', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '9', to: '24', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '10', to: '32', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '10', to: '13', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '11', to: '37', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '12', to: '36', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '13', to: '16', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '14', to: '18', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '15', to: '34', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '16', to: '23', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '17', to: '9', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '18', to: '29', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '19', to: '42', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '20', to: '33', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '21', to: '30', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '22', to: '21', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '23', to: '31', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '23', to: '22', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '24', to: '2', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '25', to: '1', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '26', to: '7', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '26', to: '5', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '28', to: '19', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '29', to: '39', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '29', to: '43', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '30', to: '40', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '31', to: '35', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '32', to: '20', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '33', to: '15', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '33', to: '27', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '34', to: '24', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '35', to: '4', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '36', to: '26', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '38', to: '11', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '39', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '40', to: '19', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '41', to: '12', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '42', to: '38', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '43', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '44', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
]; satelliteObjects = []; relations = [];
		
          var musterErgebnis = "node and: (0, 0)node realizing BT: (0, 60)node BT realized: (0, 120)node accounting for BT: (0, 180)node BT accounted: (0, 240)node xor: (25, -600)node informing employee: (25, -540)node xor: (25, -480)node BT rejected: (-75, -420)node reviewing decision: (-75, -360)node xor: (-75, -300)node BT approved: (125, -420)node checking availability of company car: (125, -360)node xor: (125, -300)node BT not rejected: (-125, -240)node revising BTA: (-125, -180)node BT rejected: (-25, -240)node company car available: (75, -240)node booking company car: (75, -180)node company car booked: (75, -120)node company car not available: (175, -240)node booking rental car: (175, -180)node rental car booked: (175, -120)node xor: (125, -60)node date of BT reached: (-50, -60)node or: (25, -1500)node BTA completed: (25, -1440)node handing in BTA: (25, -1380)node BTA handed in: (25, -1320)node reviewing BTA: (25, -1260)node xor: (25, -1200)node BTA not in accordance with requirements: (-25, -1140)node BTA in accordance with requirements: (75, -1140)node xor: (25, -1080)node noting match with requirements: (25, -1020)node match noted: (25, -960)node handing in at the manager's office: (25, -900)node xor: (25, -840)node BTA approved: (-25, -780)node noting of employee and trip period: (-25, -720)node BT registered: (-25, -660)node BTA rejected: (75, -780)node BTA required: (25, -1620)node fill BTA: (25, -1560)noting match with requirements -> match noted: [(25, -995)(25, -985)]BTA completed -> handing in BTA: [(25, -1415)(25, -1405)]noting of employee and trip period -> BT registered: [(-25, -695)(-25, -685)]company car booked -> xor: [(75, -95)(75, -60)(100, -60)]BTA in accordance with requirements -> xor: [(75, -1115)(75, -1080)(50, -1080)]informing employee -> xor: [(25, -515)(25, -505)]BTA not in accordance with requirements -> xor: [(-25, -1115)(-25, -1080)(0, -1080)]xor -> informing employee: [(25, -575)(25, -565)]fill BTA -> or: [(25, -1535)(25, -1525)]xor -> BT rejected: [(0, -480)(-75, -480)(-75, -445)]xor -> BT approved: [(50, -480)(125, -480)(125, -445)]accounting for BT -> BT accounted: [(0, 205)(0, 215)]BTA handed in -> reviewing BTA: [(25, -1295)(25, -1285)]BT approved -> checking availability of company car: [(125, -395)(125, -385)]match noted -> handing in at the manager's office: [(25, -935)(25, -925)]BT not rejected -> revising BTA: [(-125, -215)(-125, -205)]checking availability of company car -> xor: [(125, -335)(125, -325)]BTA required -> fill BTA: [(25, -1595)(25, -1585)]handing in at the manager's office -> xor: [(25, -875)(25, -865)]and -> realizing BT: [(0, 25)(0, 35)]reviewing decision -> xor: [(-75, -335)(-75, -325)]booking rental car -> rental car booked: [(175, -155)(175, -145)]company car not available -> booking rental car: [(175, -215)(175, -205)]xor -> company car available: [(100, -300)(75, -300)(75, -265)]xor -> company car not available: [(150, -300)(175, -300)(175, -265)]or -> BTA completed: [(25, -1475)(25, -1465)]xor -> noting match with requirements: [(25, -1055)(25, -1045)]xor -> BTA not in accordance with requirements: [(0, -1200)(-25, -1200)(-25, -1165)]xor -> BTA in accordance with requirements: [(50, -1200)(75, -1200)(75, -1165)]date of BT reached -> and: [(-50, -35)(-50, 0)(-25, 0)]xor -> BTA approved: [(0, -840)(-25, -840)(-25, -805)]xor -> BTA rejected: [(50, -840)(75, -840)(75, -805)]rental car booked -> xor: [(175, -95)(175, -60)(150, -60)]company car available -> booking company car: [(75, -215)(75, -205)]BT rejected -> reviewing decision: [(-75, -395)(-75, -385)]xor -> BT not rejected: [(-100, -300)(-125, -300)(-125, -265)]xor -> BT rejected: [(-50, -300)(-25, -300)(-25, -265)]booking company car -> company car booked: [(75, -155)(75, -145)]reviewing BTA -> xor: [(25, -1235)(25, -1225)]BT realized -> accounting for BT: [(0, 145)(0, 155)]BTA approved -> noting of employee and trip period: [(-25, -755)(-25, -745)]xor -> and: [(125, -35)(125, 0)(25, 0)]handing in BTA -> BTA handed in: [(25, -1355)(25, -1345)]realizing BT -> BT realized: [(0, 85)(0, 95)]BTA rejected -> xor: [(75, -755)(75, -600)(50, -600)]BT registered -> xor: [(-25, -635)(-25, -600)(0, -600)]revising BTA -> or: [(-175, -180)(-125, -180)(-125, -1500)(0, -1500)]";

          expect(setNodesAndEdges("test", undefined, nodes, edges, satelliteObjects, relations, 0, 10, 0, 0, true)).toEqual(musterErgebnis);
      }); 
	 
	        it("Exams Solution 2 [100, 50, 0, 0]", function () {
		
		  var nodes = [{id: '2', label: 'Business Trip Application', type: 'event', group: 'Solution_2', color: '#FF8080', level: 1}
,{id: '9', label: 'Fill Business Trip Application', type: 'function', group: 'Solution_2', color: '#80ff80', level: 2}
,{id: '18', label: 'or', type: 'operator', group: 'Solution_2', color: 'gray', level: 3}
,{id: '19', label: 'Application Filled', type: 'event', group: 'Solution_2', color: '#FF8080', level: 4}
,{id: '32', label: 'Hand in Application', type: 'function', group: 'Solution_2', color: '#80ff80', level: 5}
,{id: '33', label: 'Application Handed in', type: 'event', group: 'Solution_2', color: '#FF8080', level: 6}
,{id: '24', label: 'Checking Application', type: 'function', group: 'Solution_2', color: '#80ff80', level: 7}
,{id: '11', label: 'xor', type: 'operator', group: 'Solution_2', color: 'gray', level: 8}
,{id: '39', label: 'Application accord. to requirements', type: 'event', group: 'Solution_2', color: '#FF8080', level: 9}
,{id: '30', label: 'Making notes accordingly', type: 'function', group: 'Solution_2', color: '#80ff80', level: 10}
,{id: '17', label: 'Notes Made', type: 'event', group: 'Solution_2', color: '#FF8080', level: 11}
,{id: '3', label: 'Hand in request with note', type: 'function', group: 'Solution_2', color: '#80ff80', level: 12}
,{id: '15', label: 'xor', type: 'operator', group: 'Solution_2', color: 'gray', level: 13}
,{id: '21', label: 'Request Approved', type: 'event', group: 'Solution_2', color: '#FF8080', level: 14}
,{id: '16', label: 'Making note of employee & period of trip', type: 'function', group: 'Solution_2', color: '#80ff80', level: 15}
,{id: '20', label: 'Note Made', type: 'event', group: 'Solution_2', color: '#FF8080', level: 16}
,{id: '29', label: 'Informing Employee', type: 'function', group: 'Solution_2', color: '#80ff80', level: 17}
,{id: '36', label: 'xor', type: 'operator', group: 'Solution_2', color: 'gray', level: 18}
,{id: '14', label: 'Request Rejected', type: 'event', group: 'Solution_2', color: '#FF8080', level: 19}
,{id: '38', label: 'Checking the decision', type: 'function', group: 'Solution_2', color: '#80ff80', level: 20}
,{id: '37', label: 'xor', type: 'operator', group: 'Solution_2', color: 'gray', level: 21}
,{id: '34', label: 'Discard the trip', type: 'event', group: 'Solution_2', color: '#FF8080', level: 22}
,{id: '12', label: 'Decision not convincing', type: 'event', group: 'Solution_2', color: '#FF8080', level: 22}
,{id: '13', label: 'Resending Application', type: 'function', group: 'Solution_2', color: '#80ff80', level: 23}
,{id: '6', label: 'Request Approved', type: 'event', group: 'Solution_2', color: '#FF8080', level: 19}
,{id: '7', label: 'Asking company car availability', type: 'function', group: 'Solution_2', color: '#80ff80', level: 20}
,{id: '28', label: 'xor', type: 'operator', group: 'Solution_2', color: 'gray', level: 21}
,{id: '22', label: 'Company car not available', type: 'event', group: 'Solution_2', color: '#FF8080', level: 22}
,{id: '1', label: 'Ordering rental car', type: 'function', group: 'Solution_2', color: '#80ff80', level: 23}
,{id: '31', label: 'Rental car ordered', type: 'event', group: 'Solution_2', color: '#FF8080', level: 24}
,{id: '8', label: 'Realizing Business Trip', type: 'function', group: 'Solution_2', color: '#80ff80', level: 25}
,{id: '35', label: 'Business Trip Realized', type: 'event', group: 'Solution_2', color: '#FF8080', level: 26}
,{id: '25', label: 'Accounting for Business Trip', type: 'function', group: 'Solution_2', color: '#80ff80', level: 27}
,{id: '5', label: 'Business Trip Accounted', type: 'event', group: 'Solution_2', color: '#FF8080', level: 28}
,{id: '27', label: 'Company car available', type: 'event', group: 'Solution_2', color: '#FF8080', level: 22}
,{id: '10', label: 'Booking company car', type: 'function', group: 'Solution_2', color: '#80ff80', level: 23}
,{id: '4', label: 'Company car booked', type: 'event', group: 'Solution_2', color: '#FF8080', level: 24}
,{id: '23', label: 'Request Rejected', type: 'event', group: 'Solution_2', color: '#FF8080', level: 14}
,{id: '26', label: 'Application not accord. to requirements', type: 'event', group: 'Solution_2', color: '#FF8080', level: 9}
]; edges = [{from: '1', to: '31', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '2', to: '9', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '3', to: '15', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '4', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '6', to: '7', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '7', to: '28', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '8', to: '35', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '9', to: '18', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '10', to: '4', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '11', to: '39', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '11', to: '26', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '12', to: '13', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '13', to: '18', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '14', to: '38', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '15', to: '21', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '15', to: '23', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '16', to: '20', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '17', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '18', to: '19', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '19', to: '32', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '20', to: '29', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '21', to: '16', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '22', to: '1', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '23', to: '29', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '24', to: '11', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '25', to: '5', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '26', to: '30', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '27', to: '10', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '28', to: '22', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '28', to: '27', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '29', to: '36', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '30', to: '17', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '31', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '32', to: '33', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '33', to: '24', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '35', to: '25', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '36', to: '14', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '36', to: '6', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '37', to: '34', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '37', to: '12', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '38', to: '37', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '39', to: '30', arrows:'to', style: 'arrow', color: 'gray'}
]; satelliteObjects = []; relations = [];
		
          var musterErgebnis = "node Business Trip Application: (0, 0)node Fill Business Trip Application: (0, 100)node or: (0, 200)node Application Filled: (0, 300)node Hand in Application: (0, 400)node Application Handed in: (0, 500)node Checking Application: (0, 600)node xor: (0, 700)node Application accord. to requirements: (-100, 800)node Application not accord. to requirements: (100, 800)node Making notes accordingly: (0, 900)node Notes Made: (0, 1000)node Hand in request with note: (0, 1100)node xor: (0, 1200)node Request Approved: (-100, 1300)node Making note of employee & period of trip: (-100, 1400)node Note Made: (-100, 1500)node Request Rejected: (100, 1300)node Informing Employee: (0, 1600)node xor: (0, 1700)node Request Rejected: (-200, 1800)node Checking the decision: (-200, 1900)node xor: (-200, 2000)node Request Approved: (200, 1800)node Asking company car availability: (200, 1900)node xor: (200, 2000)node Discard the trip: (-300, 2100)node Decision not convincing: (-100, 2100)node Resending Application: (-100, 2200)node Company car not available: (100, 2100)node Ordering rental car: (100, 2200)node Rental car ordered: (100, 2300)node Company car available: (300, 2100)node Booking company car: (300, 2200)node Company car booked: (300, 2300)node Realizing Business Trip: (200, 2400)node Business Trip Realized: (200, 2500)node Accounting for Business Trip: (200, 2600)node Business Trip Accounted: (200, 2700)Ordering rental car -> Rental car ordered: [(100, 2225)(100, 2275)]Business Trip Application -> Fill Business Trip Application: [(0, 25)(0, 75)]Hand in request with note -> xor: [(0, 1125)(0, 1175)]Company car booked -> Realizing Business Trip: [(300, 2325)(300, 2400)(250, 2400)]Request Approved -> Asking company car availability: [(200, 1825)(200, 1875)]Asking company car availability -> xor: [(200, 1925)(200, 1975)]Realizing Business Trip -> Business Trip Realized: [(200, 2425)(200, 2475)]Fill Business Trip Application -> or: [(0, 125)(0, 175)]Booking company car -> Company car booked: [(300, 2225)(300, 2275)]xor -> Application accord. to requirements: [(-25, 700)(-100, 700)(-100, 775)]xor -> Application not accord. to requirements: [(25, 700)(100, 700)(100, 775)]Decision not convincing -> Resending Application: [(-100, 2125)(-100, 2175)]Request Rejected -> Checking the decision: [(-200, 1825)(-200, 1875)]xor -> Request Approved: [(-25, 1200)(-100, 1200)(-100, 1275)]xor -> Request Rejected: [(25, 1200)(100, 1200)(100, 1275)]Making note of employee & period of trip -> Note Made: [(-100, 1425)(-100, 1475)]Notes Made -> Hand in request with note: [(0, 1025)(0, 1075)]or -> Application Filled: [(0, 225)(0, 275)]Application Filled -> Hand in Application: [(0, 325)(0, 375)]Note Made -> Informing Employee: [(-100, 1525)(-100, 1600)(-50, 1600)]Request Approved -> Making note of employee & period of trip: [(-100, 1325)(-100, 1375)]Company car not available -> Ordering rental car: [(100, 2125)(100, 2175)]Request Rejected -> Informing Employee: [(100, 1325)(100, 1600)(50, 1600)]Checking Application -> xor: [(0, 625)(0, 675)]Accounting for Business Trip -> Business Trip Accounted: [(200, 2625)(200, 2675)]Application not accord. to requirements -> Making notes accordingly: [(100, 825)(100, 900)(50, 900)]Company car available -> Booking company car: [(300, 2125)(300, 2175)]xor -> Company car not available: [(175, 2000)(100, 2000)(100, 2075)]xor -> Company car available: [(225, 2000)(300, 2000)(300, 2075)]Informing Employee -> xor: [(0, 1625)(0, 1675)]Making notes accordingly -> Notes Made: [(0, 925)(0, 975)]Rental car ordered -> Realizing Business Trip: [(100, 2325)(100, 2400)(150, 2400)]Hand in Application -> Application Handed in: [(0, 425)(0, 475)]Application Handed in -> Checking Application: [(0, 525)(0, 575)]Business Trip Realized -> Accounting for Business Trip: [(200, 2525)(200, 2575)]xor -> Request Rejected: [(-25, 1700)(-200, 1700)(-200, 1775)]xor -> Request Approved: [(25, 1700)(200, 1700)(200, 1775)]xor -> Discard the trip: [(-225, 2000)(-300, 2000)(-300, 2075)]xor -> Decision not convincing: [(-175, 2000)(-100, 2000)(-100, 2075)]Checking the decision -> xor: [(-200, 1925)(-200, 1975)]Application accord. to requirements -> Making notes accordingly: [(-100, 825)(-100, 900)(-50, 900)]Resending Application -> or: [(-150, 2200)(-450, 2200)(-450, 200)(-25, 200)]";

          expect(setNodesAndEdges("test", undefined, nodes, edges, satelliteObjects, relations, 100, 50, 0, 0, true)).toEqual(musterErgebnis);
      });
	
	
	
      it("Exams Solution 2 [0, 10, 0, 0]", function () {
		
		  var  nodes = [{id: '2', label: 'Business Trip Application', type: 'event', group: 'Solution_2', color: '#FF8080', level: 1}
,{id: '9', label: 'Fill Business Trip Application', type: 'function', group: 'Solution_2', color: '#80ff80', level: 2}
,{id: '18', label: 'or', type: 'operator', group: 'Solution_2', color: 'gray', level: 3}
,{id: '19', label: 'Application Filled', type: 'event', group: 'Solution_2', color: '#FF8080', level: 4}
,{id: '32', label: 'Hand in Application', type: 'function', group: 'Solution_2', color: '#80ff80', level: 5}
,{id: '33', label: 'Application Handed in', type: 'event', group: 'Solution_2', color: '#FF8080', level: 6}
,{id: '24', label: 'Checking Application', type: 'function', group: 'Solution_2', color: '#80ff80', level: 7}
,{id: '11', label: 'xor', type: 'operator', group: 'Solution_2', color: 'gray', level: 8}
,{id: '39', label: 'Application accord. to requirements', type: 'event', group: 'Solution_2', color: '#FF8080', level: 9}
,{id: '30', label: 'Making notes accordingly', type: 'function', group: 'Solution_2', color: '#80ff80', level: 10}
,{id: '17', label: 'Notes Made', type: 'event', group: 'Solution_2', color: '#FF8080', level: 11}
,{id: '3', label: 'Hand in request with note', type: 'function', group: 'Solution_2', color: '#80ff80', level: 12}
,{id: '15', label: 'xor', type: 'operator', group: 'Solution_2', color: 'gray', level: 13}
,{id: '21', label: 'Request Approved', type: 'event', group: 'Solution_2', color: '#FF8080', level: 14}
,{id: '16', label: 'Making note of employee & period of trip', type: 'function', group: 'Solution_2', color: '#80ff80', level: 15}
,{id: '20', label: 'Note Made', type: 'event', group: 'Solution_2', color: '#FF8080', level: 16}
,{id: '29', label: 'Informing Employee', type: 'function', group: 'Solution_2', color: '#80ff80', level: 17}
,{id: '36', label: 'xor', type: 'operator', group: 'Solution_2', color: 'gray', level: 18}
,{id: '14', label: 'Request Rejected', type: 'event', group: 'Solution_2', color: '#FF8080', level: 19}
,{id: '38', label: 'Checking the decision', type: 'function', group: 'Solution_2', color: '#80ff80', level: 20}
,{id: '37', label: 'xor', type: 'operator', group: 'Solution_2', color: 'gray', level: 21}
,{id: '34', label: 'Discard the trip', type: 'event', group: 'Solution_2', color: '#FF8080', level: 22}
,{id: '12', label: 'Decision not convincing', type: 'event', group: 'Solution_2', color: '#FF8080', level: 22}
,{id: '13', label: 'Resending Application', type: 'function', group: 'Solution_2', color: '#80ff80', level: 23}
,{id: '6', label: 'Request Approved', type: 'event', group: 'Solution_2', color: '#FF8080', level: 19}
,{id: '7', label: 'Asking company car availability', type: 'function', group: 'Solution_2', color: '#80ff80', level: 20}
,{id: '28', label: 'xor', type: 'operator', group: 'Solution_2', color: 'gray', level: 21}
,{id: '22', label: 'Company car not available', type: 'event', group: 'Solution_2', color: '#FF8080', level: 22}
,{id: '1', label: 'Ordering rental car', type: 'function', group: 'Solution_2', color: '#80ff80', level: 23}
,{id: '31', label: 'Rental car ordered', type: 'event', group: 'Solution_2', color: '#FF8080', level: 24}
,{id: '8', label: 'Realizing Business Trip', type: 'function', group: 'Solution_2', color: '#80ff80', level: 25}
,{id: '35', label: 'Business Trip Realized', type: 'event', group: 'Solution_2', color: '#FF8080', level: 26}
,{id: '25', label: 'Accounting for Business Trip', type: 'function', group: 'Solution_2', color: '#80ff80', level: 27}
,{id: '5', label: 'Business Trip Accounted', type: 'event', group: 'Solution_2', color: '#FF8080', level: 28}
,{id: '27', label: 'Company car available', type: 'event', group: 'Solution_2', color: '#FF8080', level: 22}
,{id: '10', label: 'Booking company car', type: 'function', group: 'Solution_2', color: '#80ff80', level: 23}
,{id: '4', label: 'Company car booked', type: 'event', group: 'Solution_2', color: '#FF8080', level: 24}
,{id: '23', label: 'Request Rejected', type: 'event', group: 'Solution_2', color: '#FF8080', level: 14}
,{id: '26', label: 'Application not accord. to requirements', type: 'event', group: 'Solution_2', color: '#FF8080', level: 9}
]; edges = [{from: '1', to: '31', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '2', to: '9', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '3', to: '15', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '4', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '6', to: '7', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '7', to: '28', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '8', to: '35', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '9', to: '18', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '10', to: '4', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '11', to: '39', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '11', to: '26', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '12', to: '13', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '13', to: '18', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '14', to: '38', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '15', to: '21', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '15', to: '23', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '16', to: '20', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '17', to: '3', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '18', to: '19', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '19', to: '32', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '20', to: '29', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '21', to: '16', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '22', to: '1', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '23', to: '29', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '24', to: '11', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '25', to: '5', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '26', to: '30', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '27', to: '10', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '28', to: '22', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '28', to: '27', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '29', to: '36', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '30', to: '17', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '31', to: '8', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '32', to: '33', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '33', to: '24', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '35', to: '25', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '36', to: '14', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '36', to: '6', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '37', to: '34', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '37', to: '12', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '38', to: '37', arrows:'to', style: 'arrow', color: 'gray'}
,{from: '39', to: '30', arrows:'to', style: 'arrow', color: 'gray'}
]; satelliteObjects = []; relations = [];
		
          var musterErgebnis = "node Business Trip Application: (0, 0)node Fill Business Trip Application: (0, 60)node or: (0, 120)node Application Filled: (0, 180)node Hand in Application: (0, 240)node Application Handed in: (0, 300)node Checking Application: (0, 360)node xor: (0, 420)node Application accord. to requirements: (-50, 480)node Application not accord. to requirements: (50, 480)node Making notes accordingly: (0, 540)node Notes Made: (0, 600)node Hand in request with note: (0, 660)node xor: (0, 720)node Request Approved: (-50, 780)node Making note of employee & period of trip: (-50, 840)node Note Made: (-50, 900)node Request Rejected: (50, 780)node Informing Employee: (0, 960)node xor: (0, 1020)node Request Rejected: (-100, 1080)node Checking the decision: (-100, 1140)node xor: (-100, 1200)node Request Approved: (100, 1080)node Asking company car availability: (100, 1140)node xor: (100, 1200)node Discard the trip: (-150, 1260)node Decision not convincing: (-50, 1260)node Resending Application: (-50, 1320)node Company car not available: (50, 1260)node Ordering rental car: (50, 1320)node Rental car ordered: (50, 1380)node Company car available: (150, 1260)node Booking company car: (150, 1320)node Company car booked: (150, 1380)node Realizing Business Trip: (100, 1440)node Business Trip Realized: (100, 1500)node Accounting for Business Trip: (100, 1560)node Business Trip Accounted: (100, 1620)Ordering rental car -> Rental car ordered: [(50, 1345)(50, 1355)]Business Trip Application -> Fill Business Trip Application: [(0, 25)(0, 35)]Hand in request with note -> xor: [(0, 685)(0, 695)]Company car booked -> Realizing Business Trip: [(150, 1405)(150, 1440)(150, 1440)]Request Approved -> Asking company car availability: [(100, 1105)(100, 1115)]Asking company car availability -> xor: [(100, 1165)(100, 1175)]Realizing Business Trip -> Business Trip Realized: [(100, 1465)(100, 1475)]Fill Business Trip Application -> or: [(0, 85)(0, 95)]Booking company car -> Company car booked: [(150, 1345)(150, 1355)]xor -> Application accord. to requirements: [(-25, 420)(-50, 420)(-50, 455)]xor -> Application not accord. to requirements: [(25, 420)(50, 420)(50, 455)]Decision not convincing -> Resending Application: [(-50, 1285)(-50, 1295)]Request Rejected -> Checking the decision: [(-100, 1105)(-100, 1115)]xor -> Request Approved: [(-25, 720)(-50, 720)(-50, 755)]xor -> Request Rejected: [(25, 720)(50, 720)(50, 755)]Making note of employee & period of trip -> Note Made: [(-50, 865)(-50, 875)]Notes Made -> Hand in request with note: [(0, 625)(0, 635)]or -> Application Filled: [(0, 145)(0, 155)]Application Filled -> Hand in Application: [(0, 205)(0, 215)]Note Made -> Informing Employee: [(-50, 925)(-50, 960)(-50, 960)]Request Approved -> Making note of employee & period of trip: [(-50, 805)(-50, 815)]Company car not available -> Ordering rental car: [(50, 1285)(50, 1295)]Request Rejected -> Informing Employee: [(50, 805)(50, 960)(50, 960)]Checking Application -> xor: [(0, 385)(0, 395)]Accounting for Business Trip -> Business Trip Accounted: [(100, 1585)(100, 1595)]Application not accord. to requirements -> Making notes accordingly: [(50, 505)(50, 540)(50, 540)]Company car available -> Booking company car: [(150, 1285)(150, 1295)]xor -> Company car not available: [(75, 1200)(50, 1200)(50, 1235)]xor -> Company car available: [(125, 1200)(150, 1200)(150, 1235)]Informing Employee -> xor: [(0, 985)(0, 995)]Making notes accordingly -> Notes Made: [(0, 565)(0, 575)]Rental car ordered -> Realizing Business Trip: [(50, 1405)(50, 1440)(50, 1440)]Hand in Application -> Application Handed in: [(0, 265)(0, 275)]Application Handed in -> Checking Application: [(0, 325)(0, 335)]Business Trip Realized -> Accounting for Business Trip: [(100, 1525)(100, 1535)]xor -> Request Rejected: [(-25, 1020)(-100, 1020)(-100, 1055)]xor -> Request Approved: [(25, 1020)(100, 1020)(100, 1055)]xor -> Discard the trip: [(-125, 1200)(-150, 1200)(-150, 1235)]xor -> Decision not convincing: [(-75, 1200)(-50, 1200)(-50, 1235)]Checking the decision -> xor: [(-100, 1165)(-100, 1175)]Application accord. to requirements -> Making notes accordingly: [(-50, 505)(-50, 540)(-50, 540)]Resending Application -> or: [(-100, 1320)(-150, 1320)(-150, 120)(-25, 120)]";

          expect(setNodesAndEdges("test", undefined, nodes, edges, satelliteObjects, relations, 0, 10, 0, 0, true)).toEqual(musterErgebnis);
      });
	  
	  
	  
});