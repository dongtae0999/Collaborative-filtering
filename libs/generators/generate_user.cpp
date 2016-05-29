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
	return rand() % 10;
}

int generateGenre()
{
	return rand() % 20;
}

string generateName(string &name)
{
	static const char alphanum[] =
        "0123456789"
        "ABCDEFGHIJKLMNOPQRSTUVWXYZ"
        "abcdefghijklmnopqrstuvwxyz";

    for (int i = 0; i < 15; ++i) {
       name.push_back(alphanum[rand() % (sizeof(alphanum) - 1)]);
    }

    return name;
}

int main()
{
	string genre = "";
	vector<string> genres;
	vector<string>::iterator it;
	string name;
	string passwd = "\$2y\$10\$zmfHaUSvsCEqWVGpQHl7duD4QR8sBV.KpFGNE8H4EmhYL6ent1BS6";

	string genresList[] = {"Adult Contemporary", "Alternative", "Christian & Gospel", "Country", "Dance", "Electronic", "Electronica",
							"French Pop", "German Folk", "Heavy Metal", "Hip Hop/Rap", "Hip-Hop", "House", "Latin Urban", "Pop",
 							"Pop in Spanish", "Pop/Rock", "R&B/Soul", "Reggae", "Rock", "Sertanejo", "Singer/Songwriter"};

 	cout << "INSERT INTO `user` (`id`, `nickname`, `password`, `role`, `genre1`, `genre2`, `genre3`) VALUES"<<endl;
	cout << "(11137,'admin','$2y$10$zmfHaUSvsCEqWVGpQHl7duD4QR8sBV.KpFGNE8H4EmhYL6ent1BS6','admin','Latin Urban','Heavy Metal','French Pop')," << endl;


	for (int i = 1; i < USERS_COUNT; ++i)
	{
		for(int k = 0;k<3; ++k)
		{
			while(true)
			{
				genre = genresList[generateGenre()];
				it = find (genres.begin(), genres.end(), genre);
			  	if (it != genres.end())
			  		continue;
			  	else
			  		break;
			}
			genres.push_back(genre);
		}

		cout << "(" << i <<",'" << generateName(name) << "\',\'" << passwd << "','member'" <<"," << "\'" << genres.back() << "\',";
		genres.pop_back();
		cout << "'" << genres.back() << "\',"; 
		genres.pop_back();
		cout<< "'" << genres.back() << "'";
		genres.pop_back();
		name.clear();

		if((i + 1) >= USERS_COUNT)
				cout <<");\n";
			else
				cout <<"),\n";
	}
	return 0;
}
