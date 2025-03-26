@include('frontend.header')
<style>
    body{
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }
    footer{
        margin-top: auto;
    }
</style>
<table class="table table-sort table-arrows" style="margin: 20px">
    <thead>
    <tr>
        <th scope="col">Article Title</th>
        <th scope="col" class="dates-dmy-sort">Publication Date</th>
    </tr>
    </thead>
    <tbody>
    @foreach($articles as $article)
    <tr>
        <td><a href="{{$article->url}}" target="_blank">{{$article->title}}</a></td>
        <td>{{date("d.m.Y", strtotime($article->publication_date))}}</td>
    </tr>
    @endforeach
    </tbody>
</table>
<script src="https://cdn.jsdelivr.net/npm/table-sort-js/table-sort.min.js"></script>
@include('frontend.footer')
