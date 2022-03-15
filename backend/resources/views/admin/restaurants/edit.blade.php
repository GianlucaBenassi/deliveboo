@extends('layouts.app')

@section('content')
    <div class="container">

        <h1 class="text-center mb-3">Modifica le informazioni del ristorante</h1>

        <form action="{{route('restaurants.update', $restaurant->id)}}" method="Post" class="mb-5" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            {{-- restaurant name --}}
            <div class="form-group">
                <label for="name">Nome</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Aggiungi il nome del ristorante"  maxlength="110" required value="{{old('name', $restaurant->name)}}">
                @error('name')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            {{-- restaurant description --}}
            <div class="form-group">
                <label for="description">Descrizione</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" placeholder="Descrivi il tuo ristorante" rows="10">{{old('description', $restaurant->description)}}</textarea>
                @error('description')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            {{-- restaurant email --}}

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="Aggiungi l'Email"  maxlength="100" required value="{{old('email', $restaurant->email)}}">
                @error('email')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            {{-- restaurant address --}}
           <div class="form-group">
                <label for="address">Indirizzo</label>
                <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" placeholder="Aggiungi l'indirizzo"  maxlength="255" required value="{{old('address', $restaurant->address)}}">
                @error('address')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            {{-- restaurant city --}}
            <div class="form-group d-none">
                <label for="city">Città</label>
                <input type="text" class="form-control @error('city') is-invalid @enderror" id="city" name="city" placeholder="Aggiungi la città"  maxlength="100" required value="Roma">
                @error('city')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

             {{-- restaurant country --}}
            <div class="form-group d-none">
                <label for="country">Paese</label>
                <input type="text" class="form-control @error('country') is-invalid @enderror" id="country" name="country" placeholder="Aggiungi il paese"  maxlength="255" required value="Italia">
                @error('country')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

             {{-- restaurant post_code --}}
            <div class="form-group d-none">
                <label for="post_code">Codice postale</label>
                <input type="text" class="form-control @error('post_code') is-invalid @enderror" id="post_code" name="post_code" placeholder="Aggiungi il codice postale"  maxlength="255" required value="00100" pattern="[0-9]{5}">
                @error('post_code')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

             {{-- restaurant phone --}}
            <div class="form-group">
                <label for="phone">Telefono</label>
                <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" placeholder="Aggiungi il numero di telefono del ristorante"  pattern="[0-9]{10,15}" required value="{{old('phone', $restaurant->phone)}}">
                @error('phone')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            {{-- categories checkbox --}}
            <div class="form-group">
                <h2 class="d-block">Categorie</h2>
                <ul class="edit-container">
                @foreach ($categories as $category)
                    <li @error('categories') is-invalid @enderror">
                        @if (old("categories"))
                            <input class="form-check-input" type="checkbox" id="{{$category->name}}" value="{{$category->id}}" name="categories[]" {{in_array($category->id, old("categories", [])) ? 'checked' : ''}}>
                        @else
                            <input class="form-check-input" type="checkbox" id="{{$category->name}}" value="{{$category->id}}" name="categories[]" {{$restaurant->categories->contains($category) ? 'checked' : ''}}>
                        @endif
                        <label class="form-check-label" for="{{$category->name}}">{{$category->name}}</label>
                    </li>
                @endforeach
                </ul>
                @error('categories')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            {{-- add image upolad --}}
            <div class="form-group">
                @if($restaurant->imgage_cover != NULL)
                    <img class="img-thumbnail float-right" src="{{ asset('storage/' . $restaurant->image_cover) }}" alt="{{$restaurant->name}} image">
                @endif
                <label for="image">Immagine</label>
                <input class="form-control @error('image_cover') is-invalid @enderror" id="image" type="file" name="image_cover" value="{{ old('image_cover', $restaurant->image_cover) }}">
                @error('image_cover')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- submit button --}}
            <button type="submit" class="btn btn-primary">Aggiorna le informazioni</button>

        </form>

        <a href="{{route('restaurants.index')}}"><button type="button" class="btn btn-dark">Dashboard ristorante</button></a>
    </div>
@endsection
