import { HeaderNames } from "../../domain";
import { Environment } from "../../environment";
import { newGuid } from "../../framework";

import { CharacterCreationStats } from "./character-creation.model";

export class CharacterCreationClient {

  constructor(
    private readonly _baseUrl: string
  ) { }

  public async generateStats(): Promise<CharacterCreationStats> {
    const response = await fetch(
      `${this._baseUrl}/api/v1/character-creation/generate`,
      {
        method: "POST",
        credentials: "include",
        headers: {
          [HeaderNames.Platform]: Environment.platform,
          [HeaderNames.RequestId]: newGuid(),

        }
      }
    );

    const content = JSON.parse(await response.text());
    return content.payload;
  }

}
