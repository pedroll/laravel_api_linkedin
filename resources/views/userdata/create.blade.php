@extends('layouts.app')

@section('content')
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="container">
        <h2>Create User</h2>
        <form action="{{ route('user.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                @if ($errors->has('name'))
                    <span class="text-danger">{{ $errors->first('name') }}</span>
                @endif
                <input type="text" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" id="name"
                       name="name" value="{{ old('name') }}" required>
            </div>
            <div class=" mb-3">
                <label for="email" class="form-label">Email</label>
                @if( $errors->has('email') )
                    <span class="text-danger">{{ $errors->first('email') }}</span>
                @endif
                <input type="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" id="email"
                       name="email" value="{{ old('email') }}" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                @if( $errors->has('password') )
                    <span class="text-danger">{{ $errors->first('password') }}</span>
                @endif
                <input type="password" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                       id="password" name="password" value="{{ old('password') }}" required>
            </div>
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                @if( $errors->has('password_confirmation') )
                    <span class="text-danger">{{ $errors->first('password_confirmation') }}</span>
                @endif
                <input type="password"
                       class="form-control {{ $errors->has('password_confirmation') ? 'is-invalid' : '' }}"
                       id="password_confirmation" name="password_confirmation"
                       value="{{ old('password_confirmation') }}" required>
            </div>
            <div class="form-group">
                <label for="foto">Foto (URL)</label>
                @if( $errors->has('foto') )
                    <span class="text-danger">{{ $errors->first('foto') }}</span>
                @endif
                <input type="text" class="form-control {{ $errors->has('foto') ? 'is-invalid' : '' }}" id="foto"
                       value="{{ old('foto') }}" placeholder="http://example.com/photo.jpg" name="foto">
            </div>
            <div class="form-group">
                <label for="edad">Edad</label>
                @if( $errors->has('edad') )
                    <span class="text-danger">{{ $errors->first('edad') }}</span>
                @endif
                <input type="number" class="form-control {{ $errors->has('edad') ? 'is-invalid' : '' }}" id="edad"
                       name="edad" value="{{ old('edad') }}" min="0" max="150" required>
            </div>
            <div class="form-group">
                <label for="acercade">Acerca de</label>
                @if( $errors->has('acercade') )
                    <span class="text-danger">{{ $errors->first('acercade') }}</span>
                @endif
                <textarea class="form-control {{ $errors->has('acercade') ? 'is-invalid' : '' }}" id="acercade"
                          name="acercade" placeholder="{{ old('acercade') }}" required></textarea>
            </div>
            <div class="form-group">
                <label for="genero">Género</label>
                @if( $errors->has('genero') )
                    <span class="text-danger">{{ $errors->first('genero') }}</span>
                @endif
                <select class="form-control {{ $errors->has('genero') ? 'is-invalid' : '' }}" id="genero" name="genero"
                        value="{{ old('genero') }}" required>
                    <option value="Masculino">Masculino</option>
                    <option value="Femenino">Femenino</option>
                    <option value="Otro">Otro</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
@endsection
