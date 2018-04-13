<?php

namespace Versoo\PassportGoogleLogin\Traits;

use Google_Client;
use Google_Service_Oauth2;
use Illuminate\Http\Request;

trait GoogleLoginTrait {
	/**
	 * Logs a App\User in using a Google token via Passport
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return \Illuminate\Database\Eloquent\Model|null
	 * @throws \League\OAuth2\Server\Exception\OAuthServerException
	 */
	public function loginGoogle( Request $request ) {
		try {
			/**
			 * Check if the 'goole_token' as passed.
			 */
			if ( $request->get( 'google_token' ) ) {

				/**
				 * Initialise Google SDK.
				 *
				 *
				 */
				$googleClient = new Google_Client( [
					'application_name' => config( 'google-passport.applicationName' ),
					// https://developers.google.com/console
					'client_id'        => config( 'google-passport.clientId' ),
					'client_secret'    => config( 'google-passport.clientSecret' ),
					// Simple API access key, also from the API console. Ensure you get
					// a Server key, and not a Browser key.
					'developer_key'    => config( 'google-passport.developerKey' ),

				] );
				$googleClient->setAccessToken( $request->get( 'google_token' ) );

				$googleAuth = new Google_Service_Oauth2( $googleClient );
				/**
				 * Make the Google request.
				 */
				$googleUser = $googleAuth->userinfo_v2_me->get( [] );

				/**
				 * Check if the user has already signed up.
				 */
				$userModel = config( 'auth.providers.users.model' );

				/**
				 * Create a new user if they haven't already signed up.
				 */
				$google_id_column  = config( 'google-passport.registration.google_id', 'google_id' );
				$name_column       = config( 'google-passport.registration.name', 'name' );
				$first_name_column = config( 'google-passport.registration.first_name', 'first_name' );
				$last_name_column  = config( 'google-passport.registration.last_name', 'last_name' );
				$email_column      = config( 'google-passport.registration.email', 'email' );
				$password_column   = config( 'google-passport.registration.password', 'password' );

				$user = $userModel::where( $google_id_column, $googleUser['id'] )->orWhere( function ( $query ) use ( $email_column, $google_id_column, $googleUser ) {
					$query->where( $email_column, $googleUser['email'] );
				} )->first();

				if ( ! $user ) {
					$user                      = new $userModel();
					$user->{$google_id_column} = $googleUser['id'];

					if ( $first_name_column ) {
						$user->{$first_name_column} = $googleUser['givenName'];
					}
					if ( $last_name_column ) {
						$user->{$last_name_column} = $googleUser['familyName'];
					}
					if ( $name_column ) {
						$user->{$name_column} = $googleUser['name'];
					}

					$user->{$email_column}    = $googleUser['email'];
					$user->{$password_column} = bcrypt( uniqid( 'plus_', true ) ); // Random password.
					$user->save();

					/**
					 * Attach a role to the user.
					 */
					if ( ! is_null( config( 'google-passport.registration.attach_role' ) ) ) {
						$user->attachRole( config( 'google-passport.registration.attach_role' ) );
					}
				}
				if ( empty( $user->{$google_id_column} ) ) {
					$user->{$google_id_column} = $googleUser['id'];
					$user->update();
				}

				return $user;
			}
		} catch ( \Exception $e ) {
			die( $e->getMessage() );
//			throw OAuthServerException::accessDenied( $e->getMessage() );
		}

		return null;
	}
}
