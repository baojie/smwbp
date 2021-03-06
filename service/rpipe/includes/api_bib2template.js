String.prototype.trim = function() { return this.replace(/^\s+|\s+$/g, ''); }

function fn_pushmap(map, key, value){
	var entry= new Array();
	if (value){
		entry.push(key);
		entry.push(value);
		map.push( entry);
	}
}

function fn_b2f ( temp, txtTpName, bibtype, tags){
	var ret = new String();
	var temp = new String(temp);

	// trim bibtype 
	temp = temp.substring(temp.indexOf('\{')+1, temp.length-1);

	// add key field
	temp = '\n  key       = ' + temp +'\n';

	// TODO: correct wrong bibtex  (missing ',' at the end of a bibtex entry)
	// this can be detected by temp.match(/=[^=\n,]*\n[^=]*=/g,',');
	
	var bib_data= new Array();       
	var counter  = new Number(100); // avoid infinit loop
	while(counter >0) {
		counter   = counter  -1;

		var entry= new Array();

		// extract key
		var index1 = new Number(0);
		index1 = temp.search(/=/)+1; 
		if (index1==0)
		break;
		var key = new String();
		key =  temp.substring(0,index1-2);
		key = key.trim();
		entry.push( key );

		// extract value
		var index2 = new Number(0);
		index2 = temp.search(/,[^,=]+=/)+1 ; 
		var value = new String();
		if (index2==0){
			index2 =   temp.length;
		} 
		value = temp.substring(index1,index2) ;

		// trim borders
		value= value.trim();
		value= value.replace(/,$/ ,'');
		value= value.replace(/\n/g ,'');
		value= value.replace(/\s+/g ,' ');  // should be careful
		value= value.trim();
		value= value.replace(/^{|}$|^"|"$|^'|'$/g ,'');
		if (key == 'author'){
			value = value.replace (/\s*and/g,';');

			var aryValues = value.split(';');

			value = new String();
			for(i = 0; i < aryValues .length; i++){
				var name = aryValues [i];
				tempIndex = name.indexOf(',');
				if (tempIndex>0){
					name= name.replace (/,,/g,',');
					aryNames = name.split(',');
					for(j = aryNames.length; j>0; j--){
						tempE = new String(aryNames[j-1]);
						value += tempE.trim();
						if (j>1)
							value+=" ";		
					}
				}else{
					value += name;
				}
				if (i<aryValues .length -1)
					value+="; ";		
			}
		}
		entry.push( value );

		// save key,value
		bib_data.push( entry);

		// update temp
		temp = temp.substring(index2);
	};

	// add tags
	fn_pushmap(bib_data, "tag", tags);

	//render key value pairs to template
	ret += '{{'+txtTpName+'.'+ bibtype;
	ret += '\n';
	ret += ' |bibtype = ' + bibtype ; 
	ret += '\n';
	for ( kv in  bib_data){
		ret += ' |'+ bib_data[kv][0] +' = ' +bib_data[kv][1] ;
		ret += '\n';
	}
	ret += '}}';
	return ret;
}

function fn_b2f_main (inputElementID, outputElementID, bKeepOriginal,inputTagElementID){
	var bKeepOriginal=new Boolean(bKeepOriginal);
	// constants
	var allowedEntryTypes= new Array( 
	'article','book','booklet','confernce','inbook',
	'incollection','inproceedings','manual',
	'masterthesis','misc','phdthesis',
	'proceedings','techreport','unpublished');

	var txtBibBegin= new String('\n<!-- Bibtex commented by bibtex2template (begin) \n\n');
	var txtBibEnd= new String('\n\n Bibtex commented by bibtex2template (end)-->');
	var txtTpBegin= new String('');
	var txtTpEnd= new String('<!--\n This template is generated by bibtex2template \n-->');
	var txtTpName = new String('i.publication');

	// get wiki text from editing area
	var text=document.getElementById(inputElementID).value;

	// Stop if the template is already in wiki text.
	if (text.search(txtTpName)!=-1){
		return false;
	}

	//try bibtex type, some of which are not yet supported
	for (x in allowedEntryTypes)
	{
		// use a simple regular expression format for bibtex (not allow '{','}' inside title)
		var pattern =new RegExp( "@" + allowedEntryTypes[x] + '[^{]*{(?:[^{}]|{[^{}]*})*}', 'i');

		var temp = text.match(pattern);
		if (null != temp){
			var text2 = fn_b2f(temp,txtTpName,allowedEntryTypes[x], document.getElementById(inputTagElementID).value); 
			//update wiki text in editing area
			if (bKeepOriginal==true){
				var text1 = text.replace(pattern, txtBibBegin+temp+txtBibEnd);
				document.getElementById(outputElementID).value =  txtTpBegin + text2 + txtTpEnd + text1;
			}else{
				document.getElementById(outputElementID).value =  text2;
			}
			
			break; // if we found a match, we don't need to work anymore
		}// if


	}//for

	return false;
}

function fn_b2f_wiki(){
	fn_b2f_main('wpTextbox1', 'wpTextbox1', true, '');
}
