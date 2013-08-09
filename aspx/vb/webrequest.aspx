<%@ Page  Language = "VB" Debug="true" %>
<%@ Import Namespace = "System" %>
<%@ Import Namespace = "System.Web" %>
<script runat="server" language = "VB"></script>
<%
Dim URL
Dim Str As System.IO.Stream
Dim srRead As System.IO.StreamReader
URL = "http://www.example.com/"
Try
	Dim req As System.Net.WebRequest = System.Net.WebRequest.Create(URL)
	Dim resp As System.Net.WebResponse = req.GetResponse
	Str = resp.GetResponseStream
	srRead = New System.IO.StreamReader(Str)
	Response.Write(srRead.ReadToEnd)
Catch ex As Exception
Finally
	srRead.Close()
	Str.Close()
End Try
%>
