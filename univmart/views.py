from django.shortcuts import render, redirect
from .models import Profile
from django.contrib import messages
from django.contrib.auth.hashers import make_password, check_password
from django.contrib.auth.decorators import login_required
from django.contrib.auth import login, logout, authenticate

def newsfeed(request):
    return render(request, 'newsfeed.html')

def signup(request):
    if request.method == 'POST':
        first_name = request.POST.get('first_name')
        last_name = request.POST.get('last_name')
        username = request.POST.get('username')
        email = request.POST.get('email') 
        birthdate = request.POST.get('birthdate')
        password = request.POST.get('password')

        if not all([first_name, last_name, username, email, birthdate, password]):
            messages.error(request, 'All fields are required. Please fill out every field.')
            return render(request, 'signup.html')

        if Profile.objects.filter(username=username).exists():
            messages.error(request, 'Username already exists. Please choose a different username.')
            return render(request, 'signup.html')

        if Profile.objects.filter(email_address=email).exists():
            messages.error(request, 'Email address is already in use. Please use a different email.')
            return render(request, 'signup.html')

        hashed_password = make_password(password)
        new_profile = Profile(
            first_name=first_name,
            last_name=last_name,
            username=username,
            email_address=email,
            birthdate=birthdate,
            password=hashed_password
        )
        new_profile.save()

        messages.success(request, 'Signup successful! You can now log in with your new credentials.')
        return redirect('home')

    return render(request, 'signup.html')

def home(request):
    if request.method == "POST":
        username = request.POST.get('username')
        password = request.POST.get('password')

        if not username or not password:
            messages.error(request, 'Please enter both username and password.')
            return redirect('home')

        try:
            profile = Profile.objects.get(username=username)

            if check_password(password, profile.password):
                messages.success(request, f'Welcome back, {profile.first_name}!')
                return redirect('newsfeed')
            else:
                messages.error(request, 'Incorrect password. Please try again.')
                return redirect('home')

        except Profile.DoesNotExist:
            messages.error(request, 'User not found. Please sign up first.')
            return redirect('signup')

    return render(request, "home.html")

def reset_password(request):
    if request.method == 'POST':
        email = request.POST.get('email_address') 
        new_password = request.POST.get('new_password')
        confirm_password = request.POST.get('confirm_password')

        if not all([email, new_password, confirm_password]):
            messages.error(request, 'All fields are required. Please fill out every field.')
            return render(request, 'reset_password.html')

        try:
            profile = Profile.objects.get(email_address=email)  
        except Profile.DoesNotExist:
            messages.error(request, 'Email address not found. Please check and try again.')
            return render(request, 'reset_password.html')

        if new_password != confirm_password:
            messages.error(request, 'Passwords do not match. Please re-enter your password.')
            return render(request, 'reset_password.html')

        if len(new_password) < 8:  
            messages.error(request, 'Your password must be at least 8 characters long.')
            return render(request, 'reset_password.html')

        profile.password = make_password(new_password)
        profile.save()

        messages.success(request, 'Your password has been reset successfully. You can now log in with the new password.')
        return redirect('home')

    return render(request, 'reset_password.html')
