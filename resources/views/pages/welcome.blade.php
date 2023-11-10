@extends('layouts.default')
@section('content')
<table>
   <tbody><tr>
           <td>
               <button type="button" onclick="createRegistration()" class="custom-button">&#10133; new registration</button>
           </td>
           <td>
               <button type="button" onclick="checkRegistration()" class="custom-button">&#10068; check registration</button>
           </td>
           <td>
               <button type="button" onclick="clearRegistration()" class="custom-button">&#9249; clear all registrations</button>
           </td>
       </tr>
   </tbody>
</table>
<table>
   <tr><td>Session Variable</td><td>Session Value</td></tr>
   <tr>
      <td>challenge</td>
      <td>{{ request()->session()->get('challenge') }}</td>
   </tr>
   <tr>
      <td>registrations</td>
      <td>{{ var_dump( request()->session()->get('registrations') ) }}</td>
    </tr>
</table>
@stop
@push('other-javascripts')
<script>
   async function createRegistration() {
      try {
         // check browser support
         if (!window.fetch) {
            throw new Error('Browser do not support fetch.');
         }
         if(!navigator.credentials){
            throw new Error('Browser do not support navigator.credentital')
         }
         if(navigator.credentials.create == null){
            throw new Error('Browser do not support navigator.create')
         }

         //let rep = await window.fetch('/getargs?fn=getGetArgs' + getGetParams(), {method:'GET',cache:'no-cache'});
         let rep = await window.fetch('/getargs?fn=aaa', {method:'GET',cache:'no-cache'});
         const createArgs = await rep.json();
         //alert(JSON.stringify(createArgs));

         if (createArgs.success === false) {
            throw new Error(createArgs.msg || 'unknown error occured');
         }

         // replace binary base64 data with ArrayBuffer. a other way to do this
         // is the reviver function of JSON.parse()
         recursiveBase64StrToArrayBuffer(createArgs);

         // create credentials
         const cred = await navigator.credentials.create(createArgs);
         
         // create object
         const authenticatorAttestationResponse = {
            transports: cred.response.getTransports  ? cred.response.getTransports() : null,
            clientDataJSON: cred.response.clientDataJSON  ? arrayBufferToBase64(cred.response.clientDataJSON) : null,
            attestationObject: cred.response.attestationObject ? arrayBufferToBase64(cred.response.attestationObject) : null
         };

         await window.fetch('/processCreate' , {
            method  : 'POST',
            body    : JSON.stringify(authenticatorAttestationResponse),
            cache   : 'no-cache',
            contentType: "json",
            processData: false,           
         }).then(
            response => response.text().then(
               function(data){
                  console.log(data);
               }
            )
          );
         

      } catch (err) {
         //reloadServerPreview();
         window.alert(err.message || 'unknown error occured');
      }
   }

   function recursiveBase64StrToArrayBuffer(obj) {
      let prefix = '=?BINARY?B?';
      let suffix = '?=';
      if (typeof obj === 'object') {
            for (let key in obj) {
               if (typeof obj[key] === 'string') {
                  let str = obj[key];
                  if (str.substring(0, prefix.length) === prefix && str.substring(str.length - suffix.length) === suffix) {
                        str = str.substring(prefix.length, str.length - suffix.length);

                        let binary_string = window.atob(str);
                        let len = binary_string.length;
                        let bytes = new Uint8Array(len);
                        for (let i = 0; i < len; i++)        {
                           bytes[i] = binary_string.charCodeAt(i);
                        }
                        obj[key] = bytes.buffer;
                  }
               } else {
                  recursiveBase64StrToArrayBuffer(obj[key]);
               }
            }
      }
   }
   
   function arrayBufferToBase64(buffer) {
      let binary = '';
      let bytes = new Uint8Array(buffer);
      let len = bytes.byteLength;
      for (let i = 0; i < len; i++) {
            binary += String.fromCharCode( bytes[ i ] );
      }
      return window.btoa(binary);
   }   
</script>
@endpush
