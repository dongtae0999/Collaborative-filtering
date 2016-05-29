#include <iostream>
#include <cstdlib>
#include <cstdio>
#include <strings.h>
#include <string.h>
#include <vector>

using namespace std;

#define USERS_COUNT 100
#define SONGS_PER_USER 20
#define SONGS_COUNT 50


int generateRaiting()
{
	return rand() % 10 + 1;
}

int generateSong()
{
	return rand() % SONGS_COUNT + 1;
}

int main()
{
	int ID = 1;
	int songID = 1;
	vector<int> songs;
	vector<int>::iterator it;

	cout << "INSERT INTO `rating` (`id`, `user_id`, `song_id`, `rating`) VALUES" << endl;

	for (int i = 1; i < USERS_COUNT; ++i)
	{
		for (int j = 0; j < SONGS_PER_USER; ++j)
		{
			while(true)
			{
				songID = generateSong();
				it = find (songs.begin(), songs.end(), songID);
			  	if (it != songs.end())
			  		continue;
			  	else
			  		break;
			}
			songs.push_back(songID);

			cout << "(" << ID <<"," << i <<"," << songID <<"," << generateRaiting();

			if((i + 1) >= USERS_COUNT && (j + 1) >= SONGS_PER_USER)
				cout <<");\n";
			else
				cout <<"),\n";

			ID++;
		}
		songs.clear();
	}
}